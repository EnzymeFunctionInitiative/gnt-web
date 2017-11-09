<?php


class ui {
    public static function make_upload_box($title, $file_id, $progress_bar_id, $progress_num_id, $other) {
        global $maxFileSize;
        if (!isset($maxFileSize))
            $maxFileSize = ini_get('post_max_size');

        return <<<HTML
                <div>
                    $title
                    <input type='file' name='$file_id' id='$file_id' data-url='server/php/' class="input_file">
                    <label for="$file_id" class="file_upload"><img src="images/upload.svg" /> <span>Choose a file&hellip;</span></label>
                </div>
                $other
                Maximum size is $maxFileSize.
HTML;
    }
}

?>

