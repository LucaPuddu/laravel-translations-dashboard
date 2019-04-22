<?php

namespace LPuddu\LaravelTranslationsDashboard;

use Faker\Generator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use LPuddu\LaravelTranslationsDashboard\Repositories\LanguageRepository;
use LPuddu\LaravelTranslationsDashboard\Repositories\OptionsRepository;
use LPuddu\LaravelTranslationsDashboard\Repositories\TranslationRepository;
use NumberFormatter;
use Waavi\Translation\Facades\TranslationCache;
use Waavi\Translation\Models\Language;
use Symfony\Component\HttpFoundation\Response;
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
            'searching' => !is_null($search)
        ]);
    }

    public function newPage(Request $request, TranslationRepository $translationRepository)
    {
        $success = $translationRepository->create([
            'locale' => Language::first()->locale,
            'namespace' => '*',
            'group' => $request->name,
            'item' => 'test',
            'text' => ''
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

        $defaultLocale = Language::first();

        $request->origin = $request->origin ?? $defaultLocale->locale;
        $request->destination = $request->destination ?? $defaultLocale->locale;

        $translations = Translation::where('group', $request->page)
            ->get();

        $translations = $this->getElements($translations, $search);

        if (!is_null($search)) {
            $translations = $translations
                ->filter(function (Translation $translation) use ($search) {
                    return (stripos($translation->item, $search) !== FALSE)
                        || (stripos($translation->text, $search) !== FALSE);
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
            'searching' => !is_null($search ?? null)
        ]);
    }

    /* ELEMENTS */
    public function elements(Request $request, LanguageRepository $languageRepository, OptionsRepository $optionsRepository)
    {
        $request->validate($this->getSearchValidationRules());

        $search = $request->search;

        $translations = $this->getElements(Translation::all(), $search);

        $richEditor = $optionsRepository->getValue('rich_editor');
        $defaultLocale = Language::first();

        $request->origin = $request->origin ?? $defaultLocale->locale;
        $request->destination = $request->destination ?? $defaultLocale->locale;

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
            'searching' => !is_null($search ?? null)
        ]);
    }

    public function newElement(Request $request, TranslationRepository $translationRepository)
    {
        $defaultLocale = Language::first();
        $request->destination = $request->destination ?? $defaultLocale->locale;

        $success = $translationRepository->create([
            'locale' => $request->destination,
            'namespace' => '*',
            'group' => $request->group,
            'item' => $request->item,
            'text' => ''
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
            'item' => 'required'
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
            'text' => $request->text ?? ''
        ];

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
            'searching' => !is_null($search)
        ]);
    }

    public function editLanguage(Request $request, LanguageRepository $languageRepository, TranslationRepository $translationRepository)
    {
        try {
            DB::beginTransaction();

            $data = [
                'id' => $request->id,
                'name' => $request->name,
                'locale' => $request->locale,
                'visible' => $request->visible,
            ];
            $valid = $languageRepository->validate($data);

            if (!$valid) {
                return response($languageRepository->validationErrors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $tempLang = Language::create([
                'name' => 'temp',
                'locale' => 'temp'
            ]);

            // Update all the translations
            Translation::where('locale', Language::find($request->id)->locale)->update(['locale' => $tempLang->locale]);

            // Update old language
            $languageRepository->find($request->id)->update($data);

            // Change back language name to original
            Translation::where('locale', $tempLang->locale)->update(['locale' => $request->locale]);

            // Delete temp language
            $tempLang->forceDelete();
        } catch (\Exception $e) {
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
            'rich_editor' => $optionsRepository->getValue('rich_editor') ?? false
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
                    return $percent > 90 || mb_stripos($name, $search) !== FALSE;
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
            'completions' => $completions
        ];
    }

    private function getElements(\Illuminate\Support\Collection $translations, $search = "")
    {
        if (!is_null($search)) {
            $translations = $translations
                ->filter(function (Translation $translation) use ($search) {
                    return (stripos($translation->item, $search) !== FALSE)
                        || (stripos($translation->text, $search) !== FALSE);
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
}