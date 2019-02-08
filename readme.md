# Subtitle Tools
Online tools to sync, fix and convert subtitle files.

## Installation
Required php extensions:
- [gd](http://php.net/manual/en/book.image.php) for working with images extracted from sup files

Required binaries:
- [Vobsub2srt](https://github.com/ruediger/VobSub2SRT) for converting sub/idx to srt
- [Uchardet](https://github.com/BYVoid/uchardet) for detecting text encoding
- [Tesseract v3](https://github.com/tesseract-ocr/tesseract) for OCR'ing sup images
  - [Tesseract v3 traineddata](https://github.com/tesseract-ocr/tessdata/tree/3.04.00)

Optional PECL extension:
- [rar](http://php.net/manual/en/book.rar.php) for extracting rar archives

Installation
```bash
cp .env.example .env

# Fill in the .env file

composer install

php artisan key:generate
 
php artisan migrate
 
npm i && npm run dev
```

## Queues
```bash
php artisan queue:work --queue=broadcast,default,slow-high,sub-idx,low-fast
```

## Laravel version
[Compare to laravel master](https://github.com/laravel/laravel/compare/321d9e3786bfd605fe847e34687ccfa8def5bda2...master)

## General information
* Language codes use [ISO 639-2](https://en.wikipedia.org/wiki/List_of_ISO_639-2_codes) (same as Tesseract)

## Queue workers
Subtitle Tools runs four queue workers. The supervisor config that keeps these workers running is listed below. The queue workers that handle sub/idx and sup OCR jobs are [nice](https://en.wikipedia.org/wiki/Nice_(Unix)). When they are processing a job, they always use all available processing power. Making them nice ensures that web requests and other more important work is given priority.

<details>
    <summary>Supervisor config</summary>
    
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
</details>

-------------
 
-------------
 
-------------
 
-------------
 # OLD
 # OLD
 # OLD


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
* Add the `NewFormat` class to the `$formats` array in `\App\Subtitles\TextFileFormat`
