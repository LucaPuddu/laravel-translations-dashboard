<?php

namespace LPuddu\LaravelTranslationsDashboard;

use Faker\Generator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use LPuddu\LaravelTranslationsDashboard\Models\Language;
use LPuddu\LaravelTranslationsDashboard\Repositories\LanguageRepository;
use LPuddu\LaravelTranslationsDashboard\Repositories\OptionsRepository;
use LPuddu\LaravelTranslationsDashboard\Repositories\TranslationRepository;
use NumberFormatter;
use Symfony\Component\HttpFoundation\Response;
use Waavi\Translation\Facades\TranslationCache;
use Waavi\Translation\Models\Translation;

class LaravelTranslationsDashboard
{
    public function home(Request $request, LanguageRepository $languageRepository)
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::PERCENT);

        $translations = Translation::all();
        $notEmptyTranslations = $translations->where('text', '!=', '');
        $pages = $translations->groupBy('group');
        $languages = $languageRepository->count();

        if ($pages->count() * $languages) {
            $progress = $notEmptyTranslations->count() / ($pages->count() * $languages * $translations->groupBy('item')->count());
        } else {
            $progress = 0;
        }

        return view('laravel-translations-dashboard::home', [
            'pages' => $pages->count(),
            'languages' => $languages,
            'translated_translations' => $notEmptyTranslations->count(),
            'progress' => $progress * 100,
            'progress_formatted' => $formatter->format($progress),
        ]);
    }

    /* PAGES */
    public function pages(Request $request, Generator $faker, LanguageRepository $languageRepository)
    {
        $request->validate($this->getSearchValidationRules());

        $search = $request->search;

        $pages = $this->getPages($request, $languageRepository);

        return view('laravel-translations-dashboard::pages', [
            'pages' => $pages['pages'],
            'languages' => $languageRepository,
            'confirmation' => $faker->word,
            'completions' => $pages['completions'],
            'searching' => !is_null($search),
        ]);
    }

    public function newPage(Request $request, TranslationRepository $translationRepository)
    {
        $success = $translationRepository->create([
            'locale' => $this->getDefaultLocale(),
            'namespace' => '*',
            'group' => $request->name,
            'item' => 'test',
            'text' => '',
        ]);
        if (!$success) {
            return response($translationRepository->validationErrors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response("");
    }

    public function deletePage(Request $request, TranslationRepository $translationRepository)
    {
        Translation::where('group', $request->name)->delete();

        return response("");
    }

    public function editPage()
    {
        return response("");
    }

    public function page(Request $request, LanguageRepository $languageRepository, OptionsRepository $optionsRepository)
    {
        $request->validate($this->getSearchValidationRules());

        $search = $request->search;
        $richEditor = $optionsRepository->getValue('rich_editor');

        $request = $this->rememberLanguages($request);

        $translations = Translation::where('group', $request->page)
            ->get();

        $translations = $this->getElements($translations, $search);

        if (!is_null($search)) {
            $translations = $translations
                ->filter(function (Translation $translation) use ($search) {
                    return (stripos($translation->item, $search) !== false)
                        || (stripos($translation->text, $search) !== false);
                });
        }

        $originTranslations = $translations->where('locale', $request->origin);
        $destinationTranslations = $translations->where('locale', $request->destination);
        $items = $translations->groupBy('item')->keys();

        $page = new \stdClass();
        $page->name = $request->page;
        $page->items = $items;
        $page->origin_translations = $originTranslations->keyBy('item');
        $page->destination_translations = $destinationTranslations->keyBy('item');

        return view('laravel-translations-dashboard::page', [
            'page' => $page,
            'origin_language' => $languageRepository->findByLocale($request->origin),
            'destination_language' => $languageRepository->findByLocale($request->destination),
            'languages' => $languageRepository->all(),
            'rich_editor' => $richEditor == '1',
            'searching' => !is_null($search ?? null),
        ]);
    }

    /* ELEMENTS */
    public function elements(
        Request $request,
        LanguageRepository $languageRepository,
        OptionsRepository $optionsRepository
    ) {
        $request->validate($this->getSearchValidationRules());

        $search = $request->search;

        $translations = $this->getElements(Translation::all(), $search);

        $richEditor = $optionsRepository->getValue('rich_editor');

        $request = $this->rememberLanguages($request);

        $pages = $translations
            ->groupBy(['group'])
            ->map(function (\Illuminate\Support\Collection $page) use ($request) {
                $new = new \stdClass();
                $new->name = $page->first()->group;
                $new->items = $page->groupBy('item')->keys();
                $new->origin_translations = $page->where('locale', $request->origin)->keyBy('item');
                $new->destination_translations = $page->where('locale', $request->destination)->keyBy('item');

                return $new;
            });

        return view('laravel-translations-dashboard::elements', [
            'page' => $request->page,
            'pages' => $pages,
            'origin_language' => $languageRepository->findByLocale($request->origin),
            'destination_language' => $languageRepository->findByLocale($request->destination),
            'languages' => $languageRepository->all(),
            'rich_editor' => $richEditor == '1',
            'searching' => !is_null($search ?? null),
        ]);
    }

    public function newElement(Request $request, TranslationRepository $translationRepository)
    {
        $request = $this->rememberLanguages($request);

        $success = $translationRepository->create([
            'locale' => $request->destination,
            'namespace' => '*',
            'group' => $request->group,
            'item' => $request->item,
            'text' => '',
        ]);
        if (!$success) {
            return response($translationRepository->validationErrors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response("");
    }

    public function deleteElement(Request $request, TranslationRepository $translationRepository)
    {
        Translation::where('group', $request->group)->where('item', $request->item)->delete();

        return response("");
    }

    public function editElement(Request $request, TranslationRepository $translationRepository)
    {
        $validationRules = $translationRepository->getValidationRules('', '', '', '');

        $validator = Validator::make($request->all(), [
            'lang' => $validationRules['locale'],
            'group' => $validationRules['group'],
            'item' => 'required',
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Update record
        $translationToUpdate = Translation::where('group', $request->group)
            ->where('item', $request->item)
            ->where('locale', $request->lang)
            ->first();

        $data = [
            'locale' => $request->lang,
            'namespace' => '*',
            'group' => $request->group,
            'item' => $request->item,
            'text' => $request->text ?? '',
        ];

        Cache::forget("translations.{$request->lang}.{$request->group}");
        TranslationCache::flush($request->lang, $request->group, '*');

        if (!isset($translationToUpdate)) {
            $translationRepository->create($data);
        } else {
            $translationToUpdate->update($data);
        }

        return response("");
    }

    /* LANGUAGES */
    public function languages(Request $request, Generator $faker)
    {
        $request->validate($this->getSearchValidationRules());

        $search = $request->search;

        $languages = $this->getLanguages($search);

        return view('laravel-translations-dashboard::languages', [
            'languages' => $languages,
            'confirmation' => $faker->word,
            'locales' => Constants::LOCALES,
            'searching' => !is_null($search),
        ]);
    }

    public function editLanguage(
        Request $request,
        LanguageRepository $languageRepository
    ) {
        try {
            DB::beginTransaction();

            $data = [
                'id' => $request->id,
                'name' => $request->name,
                'locale' => $request->locale,
                'visible' => $request->visible,
                'rtl' => $request->rtl,
            ];
            $valid = $languageRepository->validate($data);

            if (!$valid) {
                return response($languageRepository->validationErrors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $tempLocale = 'temp';

            $language = $languageRepository->find($request->id);

            // Update all the translations
            Translation::where('locale', $language->locale)->update(['locale' => $tempLocale]);

            // Update old language
            $language->update($data);

            // Change back language name to original
            Translation::where('locale', $tempLocale)->update(['locale' => $request->locale]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        DB::commit();

        return response("");
    }

    public function deleteLanguage(Request $request, LanguageRepository $languageRepository)
    {
        try {
            $success = $languageRepository->delete($request->id);

            if (!$success) {
                return response($languageRepository->validationErrors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            return response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Delete all translations
        $lang = $languageRepository->findTrashed($request->id);
        $locale = $lang['locale'];
        Translation::where('locale', $locale)->delete();

        return response("");
    }

    public function newLanguage(Request $request, LanguageRepository $languageRepository)
    {
        $success = $languageRepository->createOrRestore($request->only(['name', 'locale']));
        if (!$success) {
            return response($languageRepository->validationErrors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response("");
    }

    /* SETTINGS */
    public function settings(OptionsRepository $optionsRepository)
    {
        return view('laravel-translations-dashboard::settings', [
            'rich_editor' => $optionsRepository->getValue('rich_editor') ?? false,
        ]);
    }

    public function editSettings(Request $request, OptionsRepository $optionsRepository)
    {
        $request->rich_editor = $request->rich_editor ?? 0;

        $success = $optionsRepository->update('rich_editor', $request->rich_editor);

        if (!$success) {
            return redirect()->back()
                ->withErrors($optionsRepository->getValidationErrors())
                ->withInput();
        }

        return redirect()->back();
    }

    /* PRIVATE METHODS */

    /**
     * @param string $locale
     * @param bool   $from
     */
    protected function rememberDefaultLocale(string $locale = null, bool $from = false)
    {
        if (!isset($locale)) {
            $locale = $this->getDefaultLocale();
        }
        if ($from) {
            $type = 'from';
        } else {
            $type = 'to';
        }

        Cookie::queue("laravel_translation_dashboard_default_locale_{$type}", $locale);
    }

    /**
     * @param bool $from
     * @return array|string|null
     */
    protected function getDefaultLocalestring(bool $from = false)
    {
        if ($from) {
            $type = 'from';
        } else {
            $type = 'to';
        }
        $savedLocale = Cookie::get(
            "laravel_translation_dashboard_default_locale_{$type}",
            $this->getDefaultLocale()
        );

        return $savedLocale;
    }

    /**
     * @param Request $request
     * @return Request
     */
    protected function rememberLanguages(Request $request): Request
    {
        $request->origin = $request->origin ?? $this->getDefaultLocalestring(true);
        $this->rememberDefaultLocale($request->origin, true);
        $request->destination = $request->destination ?? $this->getDefaultLocalestring();
        $this->rememberDefaultLocale($request->destination);

        return $request;
    }

    private function getLanguages($search = "")
    {
        return Language::where("name", "like", "%$search%")
            ->orWhere("locale", "like", "%$search%")
            ->paginate(Constants::LANGUAGES_PER_PAGE);
    }

    private function getPages(Request $request, LanguageRepository $languageRepository)
    {
        $search = $request->search;

        $formatter = new NumberFormatter('en_US', NumberFormatter::PERCENT);

        $languagesCount = $languageRepository->count();
        $translations = Translation::all();
        $pages = $translations->groupBy('group');
        $completions = [];

        if (!is_null($search)) {
            $pages = $pages
                ->filter(function (Collection $page, $name) use ($search) {
                    $percent = 0;
                    similar_text($name, $search, $percent);
                    return $percent > 90 || mb_stripos($name, $search) !== false;
                });
        }

        $pages
            ->each(function (Collection $page, $name) use ($formatter, &$completions, $languagesCount) {
                $elementsCount = $page->unique(function ($item) {
                    return $item->item;
                })->count();

                $completions[$name] = $formatter->format($page->count() / ($elementsCount * $languagesCount));
            });

        // Paginate results
        $chunks = $pages->chunk(Constants::PAGES_PER_PAGE);
        // Requested chunk between 1 and $maxPage
        $page = max(1, min($request->page ?? 1, $chunks->count()));
        $chunk = $chunks->get($page - 1);
        $pages = new LengthAwarePaginator($chunk, $pages->count(), Constants::PAGES_PER_PAGE);
        $pages->withPath('/' . $request->path());

        return [
            'pages' => $pages,
            'completions' => $completions,
        ];
    }

    public function getPage(string $locale, string $page): \Illuminate\Support\Collection
    {
        return Cache::rememberForever("translations.{$locale}.{$page}", function() use ($locale, $page) {
            return Translation::where('group', $page)
                ->where('locale', $locale)
                ->get()
                ->mapWithKeys(
                    function (Translation $translation) {
                        return [$translation->item => $translation->text];
                    }
                );
        });
    }

    private function getElements(\Illuminate\Support\Collection $translations, $search = "")
    {
        if (!is_null($search)) {
            $translations = $translations
                ->filter(function (Translation $translation) use ($search) {
                    return (stripos($translation->item, $search) !== false)
                        || (stripos($translation->text, $search) !== false);
                });
        }

        return $translations;
    }

    private function getSearchValidationRules()
    {
        return [
            'search' => 'nullable',
        ];
    }

    private function getDefaultLocale(): string
    {
        return app()->getLocale() ?? Language::first()->locale;
    }
}
