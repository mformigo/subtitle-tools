# Subtitle Tools

## Updating code
```
todo
```


## Format information



### Sub/idx
[Vobsub2srt](https://github.com/ruediger/VobSub2SRT) is used to detect languages inside _.sub_ files, using `vobsub2srt filename --langlist`.
The .sub file only contains the index of the language, not the [language code](https://www.loc.gov/standards/iso639-2/php/code_list.php).
The language code is read from the _.idx_ file using `\App\Models\IdxFile`.

For each language inside the .sub file a `\App\Jobs\ExtractSubIdxLanguage` job is made and dispatched to the `sub-idx` queue.

Extracting a language using `vobsub2srt filename --index 0` can have the following results:
* stuck processing forever
* an error and  no `filename.srt`
* an empty `filename.srt` file
* a `filename.srt` file filled with cues without any dialogue
* a valid `filename.srt`
