<?php
$Images = array_diff(scandir(dirname(__FILE__) . "/../../images"), array('.', '..'));

include_once(dirname(__FILE__) . "../../../config/config.php");
if (isset($_REQUEST['SetContent'])) {

    if (!isset($_REQUEST['Text'])) {
        $Nodes = [];
    } else {
        $Nodes = $_REQUEST['Text'];
    }
    if ($Nodes === null) {
        $Nodes = [];
    }
    $newContent = array("Text" => $Nodes);

    SetCurrentValueByDataPosition($_REQUEST['dp'], $_REQUEST['overwrite'], $newContent);
    echo "Content Updated. Refresh to see changes.";
}
if (isset($_REQUEST['Delete'])) {
    DeleteCurrentValueByDataPosition($_REQUEST['dp'], $_REQUEST['overwrite']);
    echo "Content Updated. Refresh to see changes.";
    exit;
}

if (!preg_match('/\[\]$/', $_REQUEST['dp'])) {


    $Content = GetCurrentValueByDataPosition($_REQUEST['dp'], $_REQUEST['overwrite']);
    if (!isset($Content)) {
        echo "No such Content " . $_REQUEST['dp'] . ' ' . $_REQUEST['overwrite'];
        $Content = (object)array("ImageBefore" => '', "ImageAfter" => '', "Text" => [], "SubHeader" => '');
    } else {
        $Content = (object)$Content;
    }
} else {
    $Content = (object)array("ImageBefore" => '', "ImageAfter" => '', "Text" => [], "SubHeader" => '');
}

?>
<!DOCTYPE html>
<html>
<head>


    <!-- Main Quill library -->
    <script src="//cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <!-- Theme included stylesheets -->
    <link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="//cdn.quilljs.com/1.3.6/quill.bubble.css" rel="stylesheet">
    <script src="image-upload-q.min.js"></script>
</head>
<body>

<form method="post" enctype="multipart/form-data">
    <button type="submit" name="Delete">Delete this node (careful no undo)</button>
<button onclick="SwitchEditor()" type="button">Switch Editor</button>
    <div id="TextNodes">
        <br/>
        <select id="ImageUrl" name="ImageUrl">
            <?php foreach ($Images as $image) { ?>

                <option value="<?php echo "images/" . $image; ?>" <?php if (strcmp("images/" . $image, $Content->Url) === 0) echo "selected"; ?> ><?php echo $image; ?></option>
            <?php } ?>
        </select>

        <button type="button" onclick="InsertImage()" class="fit" value="Set Content" name="SetContent">Insert Image</button>
        <?php foreach ($Content->Text as $Text) { ?>
            <div>
                <label for="Text">Html sections

                </label>
                <div class="editor"><?php echo $Text; ?></div>
                <textarea id="Editor" style="display:none;width:100%;height:40vh"
                          name="Text[]"><?php echo $Text; ?></textarea>
            </div>
        <?php } ?>

    </div>
    <?php

    if (count($Content->Text) === 0) {
        ?>
        <div>
            <label for="Text">Html sections


            </label>
            <div class="editor"></div>
            <textarea id="Editor"  style="display:none;width:100%;height:40vh"
                      name="Text[]"></textarea>
        </div>
        <?php
    }
    ?>

    <input type="hidden" name="dp" value="<?php echo $_REQUEST['dp']; ?>"/>
    <input type="hidden" name="overwrite" value="<?php echo $_REQUEST['overwrite']; ?>"/>

    <button type="submit" class="fit" value="Set Content" name="SetContent">Update/Save Content</button>
</form>
<script src="/assets/js/jquery.min.js"></script>
<script>
    var IsEditorVisible = true;
    function AddNode(node) {
        if (node) {
            $(node).before($("<div>    <label for=\"Text\">Html Node\n" +
                "                    <button onclick=\"$(this).parent().parent().remove()\">delete</button>               <button type=\"button\" class=\"  primary\" onclick=\"AddNode($(this).parent().parent())\">Add Html Node Before this one</button>\n" +
                "                </label>\n" +
                "                <textarea name=\"Text[]\"></textarea>"))
            return;
        }
        $('#TextNodes').append($("<div>    <label for=\"Text\">Html Node\n" +
            "                    <button onclick=\"$(this).parent().parent().remove()\">delete</button>               <button type=\"button\" class=\"  primary\" onclick=\"AddNode($(this).parent().parent())\">Add Html Node Before this one</button>\n" +
            "                </label>\n" +
            "                <textarea name=\"Text[]\"></textarea>"))
    }
    function InsertImage(){
        var Image = $('#ImageUrl').val()

        var range = Edtiro.getSelection();
        Edtiro.insertEmbed(range.index, 'image', '/'+ Image, Quill.sources.USER);
    }
    function SwitchEditor() {
        IsEditorVisible= !IsEditorVisible;
        $('.editor').toggle();
        $('.ql-toolbar').toggle();
      //  var Text = document.querySelector('#Editor');
      //  Text.value = Edtiro.container.firstChild.innerHTML;
        $('#Editor').toggle()

    }

    var Edtiro;
    $(document).ready(function () {

        var form = document.querySelector('form');
        form.onsubmit = function () {
            // Populate hidden form on submit
            if(IsEditorVisible) {
                var Text = document.querySelector('#Editor');
                Text.value = Edtiro.container.firstChild.innerHTML;
            }

        };
        var options = {
            debug: 'info',
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                    ['blockquote', 'code-block'],

                    [{'header': 1}, {'header': 2}],               // custom button values
                    [{'list': 'ordered'}, {'list': 'bullet'}],
                    [{'script': 'sub'}, {'script': 'super'}],      // superscript/subscript
                    [{'indent': '-1'}, {'indent': '+1'}],          // outdent/indent
                    [{'direction': 'rtl'}],                         // text direction

                    [{'size': ['small', false, 'large', 'huge']}],  // custom dropdown
                    [{'header': [1, 2, 3, 4, 5, 6, false]}],

                    [{'color': []}, {'background': []}],          // dropdown with defaults from theme
                    [{'font': []}],
                    [{'align': []}],

                    ['clean'],                                         // remove formatting button
                    ['image']
                ]
            },
            imageUpload: {
                url: 'UploadImage.php?json=true', // server url. If the url is empty then the base64 returns
                method: 'POST', // change query method, default 'POST'
                name: 'UploadImage', // custom form name
                withCredentials: false, // withCredentials
                headers: {}, // add custom headers, example { token: 'your-token'}
                // personalize successful callback and call next function to insert new url to the editor
                callbackOK: (serverResponse, next) => {
                    Edtiro.insertEmbed(range.index, 'image', serverResponse.file, Quill.sources.USER); 
                    next(serverResponse);
                },
                // personalize failed callback
                callbackKO: serverError => {
                    alert(serverError);
                }
            }
        }


        Edtiro = new Quill('.editor', options);
    })
    ;
</script>
</body>
</html>