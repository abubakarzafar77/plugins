<div class="wrap">
    <form action="?page=login_page_text" method="post">
        <div class="tr_form-box">
            <label for="name" style="max-width: 200px;margin-bottom: 10px;">Content(HTML):</label>
            <?php
                $editor_id = 'login_page_text';
                $content = (isset($page_text['html'])?$page_text['html']:'');

                wp_editor( stripslashes($content), $editor_id );
            ?>
        </div>
        <input type="submit" name="update" id="update" value=" Update " class="button" />
    </form>
</div>
<script>
    jQuery(document).ready(function () {
        var editor = CodeMirror.fromTextArea(login_page_text, {
            lineNumbers: true,
            theme: 'material',
            mode: "text/html",
            scrollbarStyle: "null",
            extraKeys: {
                "F11": function(cm) {
                    cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                },
                "Esc": function(cm) {
                    if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                }
            }
        });
    });
</script>