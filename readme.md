# Subtitle Tools

## Updating code
* Compile production assets `yarn run prod`
* Push to Master
* SSH into server
* `php artisan down`
* `git pull`
* `composer update --no-dev`
* `php artisan clear-compiled`
* `php artisan queue:restart`
* `php artisan up`
* Check if queues are still running: `sudo supervisorctl`

## Possible improvements
* Smi parsing uses `->getCues` a lot, the sorting is slow
* `\App\Subtitles\VobSub\VobSub2Srt` needs the `SubIdx` model purely for diagnostic logging, it should only require the path. The logging should happen in a different, easier to test way


## General configuration
* `phpunit.xml` sets the database to 'subtitle-tools-testing'
* `phpunit.xml` sets the filesystem disk to 'local-testing'
* `TestCase.php` deletes all files from the 'local-testing' directories before each test
* **Laravel Dusk** runs from inside vagrant homestead following [this guide](https://medium.com/@splatEric/laravel-dusk-on-homestead-dc5711987595)

## Queues and Workers
* sub-idx language extract jobs run on the **sub-idx** queue. These jobs are extremely cpu intensive.
* broadcasting happens on the **broadcast** queue
* file jobs happen on the **default** queue
* fast low prio jobs happen on **low-fast**, when the default queue is idle

## Adding a FileGroup + FileJob tool
* make a new controller that extends `FileJobController`, add routes and views
* make a new job that extends `FileJobJob`

## Adding a new Text Encoding
* Add the name of the encoding to `App\Utils\Text\TextEncoding.php`
* Add a file using the encoding to `tests/Storage/TextEncoding/`
* Add the file to `tests\Unit\TextEncodingTest.php` 

## Format information
The format of each subtitle file is identified by `\App\Subtitles\TextFileFormat`. 
It identifies based on file content, and ignores the file extension.
File extensions can't be trusted because users regularly upload files where the content does not match the extension.

### Adding a new plain-text format
* Create a `NewFormat` class that extends the abstract `TextFile`
* Make it use either the `WithFileContent` trait or the `WithFileLines` trait
* If it can be converted to `Srt`, implement the `TransformsToGenericSubtitle` interface
* Create `tests\Unit\Subtitles\NewFormatTest.php`
* Add the `NewFormat` class to the `$formats` array in `\App\Subtitles\TextFileFormat`
* Add a test for `NewFormat::isThisFormat()` to `tests\Unit\Subtitles\TextFileFormatTest.php`

### About Sub/idx
[Vobsub2srt](https://github.com/ruediger/VobSub2SRT) is used to detect languages inside _.sub_ files, using `vobsub2srt filename --langlist`.
The .sub file only contains the index of the language, not the [language code](https://www.loc.gov/standards/iso639-2/php/code_list.php).
The language code is read from the _.idx_ file using `\App\Models\IdxFile`.

For each language inside the .sub file a `\App\Jobs\ExtractSubIdxLanguage` job is made and dispatched to the `sub-idx` queue.

Extracting a language using `vobsub2srt filename --index 0` can have the following results:
* stuck processing forever
* an error and  no `filename.srt`
* an error and  an empty `filename.srt`
* no error and an empty `filename.srt`
* a `filename.srt` file filled with cues without any dialogue
* a valid `filename.srt`

Possible errors when extracting a language:
* missing the language training data
* bad alloc exception (server related?)