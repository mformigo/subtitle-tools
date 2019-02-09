# Subtitle Tools
Online tools to sync, fix and convert subtitle files.

The following repositories are part of Subtitle Tools:
- https://github.com/SjorsO/sup

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

## Laravel version
The following link can be used to keep the application in sync with Laravel's master branch: [compare to laravel master](https://github.com/laravel/laravel/compare/321d9e3786bfd605fe847e34687ccfa8def5bda2...master)

## General information
* Language codes use [ISO 639-2](https://en.wikipedia.org/wiki/List_of_ISO_639-2_codes) (same as Tesseract)

## Queues
Subtitle Tools runs four separate queue workers. The supervisor config that keeps these workers running is listed below. The queue workers that handle sub/idx and sup OCR jobs are [nice](https://en.wikipedia.org/wiki/Nice_(Unix)). OCR'ing is a CPU heavy job, it will consume all available processing power of the thread it is running on. Making them nice ensures that web requests and other more important work is given priority.

<details>
    <summary>Supervisor config</summary>

    [program:st-worker-default]
    process_name=%(program_name)s_%(process_num)02d
    command=php /var/www/st/current/artisan queue:work --queue=default --sleep=2 --tries=1
    numprocs=3
    autorestart=true
    user=www-data

    [program:st-worker-broadcast]
    process_name=%(program_name)s_%(process_num)02d
    command=php /var/www/st/current/artisan queue:work --queue=broadcast --sleep=2 --tries=2
    autorestart=true
    user=www-data

    [program:st-worker-1]
    process_name=%(program_name)s_%(process_num)02d
    command=nice php /var/www/st/current/artisan queue:work --queue=A100,A200,A300,A400,A500 --sleep=2 --tries=1
    autorestart=true
    user=www-data

    [program:st-worker-2]
    process_name=%(program_name)s_%(process_num)02d
    command=nice php /var/www/st/current/artisan queue:work --queue=B200,A100,A200,A300,A400,A500 --sleep=2 --tries=1
    autorestart=true
    user=www-data
</details>

### Processing all jobs
While debugging, you can run the following command to process all jobs:
```bash
artisan queue:work --queue=broadcast,default,B200,A100,A200,A300,A400,A500 --sleep=2 --tries=1
```

### Jobs per queue
The list below shows which queue runs which jobs. The order in which the queues are handled can be found in the supervisor config above.

<details>
    <summary>Jobs per queue</summary>

- **default**
    - All `FileJobs`
- **broadcast**
    - All events
- **A100**
    - `BuildSupSrtJob`
- **A200**
    - `ExtractSupImagesJob`
- **A300**
    - `OcrImageJob`
- **A400**
    - `OcrImageJob` (when the job in queue A300 takes too long, it is re-dispatched on this lower priority queue with a higher timeout)
- **A500**
    - `CollectSupMetaJob`
    - `CollectStoredFileMetaJob`
- **B200**
    - `ExtractSubIdxLanguageJob`
</details>

## Adding a FileGroup + FileJob tool
* Make a new controller that extends `FileJobController`, add routes and views
* Make a new job that extends `FileJob`

## Adding a new plain-text format
* Create a `NewFormat` class that extends the abstract `TextFile`
* Make it use either the `WithFileContent` trait or the `WithFileLines` trait
* If it can be converted to `Srt`, implement the `TransformsToGenericSubtitle` interface
* Add the `NewFormat` class to the `$formats` array in `\App\Subtitles\TextFileFormat`

## Contributing
Feel free to open a pull request if you want to add a new feature, or if you want to help improve the code, design, text, seo, or any other part of the website.

If you want want to discuss an idea before implementing it, you can contact me by email, twitter, or by opening an issue. 

## License
This project is open-source software licensed under the [MIT license](http://opensource.org/licenses/MIT)
