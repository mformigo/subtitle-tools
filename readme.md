# Subtitle Tools

## Queues
File jobs and diagnostics:
```bash
php artisan queue:work --queue=broadcast,default,slow-high,sub-idx,low-fast
```

## Laravel version
[Compare to laravel master](https://github.com/laravel/laravel/compare/321d9e3786bfd605fe847e34687ccfa8def5bda2...master)

## General information
* Language codes use [ISO 639-2](https://en.wikipedia.org/wiki/List_of_ISO_639-2_codes) (same as Tesseract)

## Queue workers
Subtitle Tools runs four queue workers. The supervisor config that keeps these workers running is listed below. The queue workers that handle sub/idx and sup OCR jobs are [nice](https://en.wikipedia.org/wiki/Nice_(Unix\)). When they are processing a job, they always use all available processing power. Making them nice ensures that web requests and other more important work is given priority.

```
[program:st-worker-broadcast]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/st/current/artisan queue:work --queue=broadcast --sleep=2 --tries=2
autorestart=true
user=www-data

[program:st-worker-default]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/st/current/artisan queue:work --queue=default,low-fast --sleep=2 --tries=2
autorestart=true
user=www-data

[program:st-worker-jerry]
process_name=%(program_name)s_%(process_num)02d
command=nice php /var/www/st/current/artisan queue:work --queue=larry-high,larry-default,larry-low,larry-lowest --sleep=2 --tries=1
autorestart=true
user=www-data

[program:st-worker-larry]
process_name=%(program_name)s_%(process_num)02d
command=nice php /var/www/st/current/artisan queue:work --queue=slow-high,sub-idx,larry-high,larry-default,larry-low,larry-lowest --sleep=2 --tries=1
autorestart=true
user=www-data
```


-------------
 
-------------
 
-------------
 
-------------
 # OLD
 # OLD
 # OLD

## Required mods
The status of the mods can be checked in the admin dashboard

* uchardet
* vobsub2srt
* Redis
* PHP Rar (PECL)
* PHP gd (sjorso/sup)
* Tesseract (all the traineddata, and the `tesseract` command from PATH (for PATH, install with apt-get))

## Updating code
* Compile production assets `yarn run prod`
* Push to Master
* SSH into server
* `php artisan down`
* `git pull`
* `composer install --no-dev`
* `php artisan clear-compiled`
* `php artisan queue:restart`
* `php artisan up`
* Check if queues are still running: `supervisorctl`

## Possible improvements
* Smi parsing uses `->getCues` a lot, the sorting is slow
* `\App\Subtitles\VobSub\VobSub2Srt` needs the `SubIdx` model purely for diagnostic logging, it should only require the path. The logging should happen in a different, easier to test way

## Queues and Workers
`php artisan queue:work --queue=broadcast,default,slow-high,sub-idx,low-fast`
* sup to srt jobs are run on the **slow-high** queue.
* sub-idx language extract jobs run on the **sub-idx** queue when the slow-high queue is idle
* broadcasting happens on the **broadcast** queue
* file jobs happen on the **default** queue
* fast low prio jobs happen on **low-fast**, when the default queue is idle
 
* **larry-high**: BuildSupSrtJob
* **larry-default**: ExtractSupImagesJob
* **larry-low**: OcrImageJob
* **larry-lowest**: OcrImageJob (slow)

## Adding a FileGroup + FileJob tool
* make a new controller that extends `FileJobController`, add routes and views
* make a new job that extends `FileJob`

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
* bad alloc exception (if the server doesn't have enough memory)

#### MicroDVD
The numbers are the start frame and end frame. The first cue can contain an fps hint, if present we use that fps, otherwise we assume 23.976fps.
<br/><br/>
Format: `{10}{25}line one|line two`

#### Mpl2
Mpl2 is a weird format used by the Polish. It is much like the MicroDVD format, except the numbers are decaseconds (0.1) seconds. Also, a line starting with a slash is an italic line.
<br/><br/>
Format: `[10][25]line one|/line two italic`