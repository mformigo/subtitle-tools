<?php

return [

    'status.queued'     => 'Queued',
    'status.processing' => 'Processing',
    'status.finished'   => 'Finished',
    'status.failed'     => 'Failed',

    'download' => 'Download',

    'not_a_text_file' => 'This file is not a text file',
    'cant_convert_file_to_srt' => 'This file can\'t be converted to srt',
    'cant_convert_file_to_vtt' => 'This file can\'t be converted to vtt',
    'cant_convert_file_to_plain_text' => 'This file can\'t be converted to plain text',
    'file_has_no_dialogue_to_convert' => 'This file has no dialogue',
    'file_has_no_dialogue' => 'This file has no dialogue',
    'file_is_not_srt' => 'This file is not an srt file',
    'file_can_not_be_shifted' => 'Files of this type can\'t be shifted',
    'file_can_not_be_partial_shifted' => 'Files of this type can\'t be partially shifted',
    'unknown_error' => 'An unknown error occurred',

    'subidx_no_vobsub2srt_output_file' => 'No output file after trying to extract the language',
    'subidx_empty_vobsub2srt_output_file' => 'The vobsub2srt output file was empty',
    'subidx_vobsub2srt_output_file_only_empty_cues' => 'The vobsub2srt output file only had empty cues',
    'subidx_job_failed' => 'SubIdx extract job failed',

    'sup.exception_when_reading'           => 'This sup file could not be read',
    'sup.not_a_sup_file'                   => 'This file is not a valid sup file',
    'sup.no_cues_with_dialogue'            => 'Failed converting this sup to srt (did you select the correct language?)',
    'sup.job_timed_out'                    => 'Failed converting this sup to srt (converting took too long)',
    'sup.job_failed'                       => 'Failed converting this sup to srt',
    'sup.exception_when_extracting_images' => 'Failed converting this sup to srt',

    'zip_job.unknown_error' => 'Unknown error in zip job',
    'zip_job.create_failed' => 'Failed to create archive',
    'zip_job.no_files_added' => 'No files added to zip',
    'zip_job.close_failed' => 'Failed to close/save archive',

    'archive.not_available_yet' => 'Not available yet',
    'archive.request' => 'Request zip file',
    'archive.processing' => 'Generating zip file...',
    'archive.failed' => 'Failed',
    'archive.download' => 'Download zip',

    'pinyin.can_not_make_pinyin_subtitles_from_this_file_type' => 'This file type can not be made in to pinyin subtitles',
    'pinyin.unknown_mode' => 'Unknown error',
    'pinyin.subtitles_have_no_chinese' => 'The subtitle file has no Chinese to change to pinyin',

    'cant_merge_these_subtitles' => 'Can not merge these subtitles',
];