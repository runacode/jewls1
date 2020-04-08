<?php


include_once(dirname(__FILE__) . "../../../config/config.php");
$Structures = array_diff(scandir("{$BasePath}Structures/Pages"), array('.', '..'));
$OverwritePath = "{$BasePath}config/Pages/{$_REQUEST['overwrite']}";
$overwritedata = json_decode(file_get_contents($OverwritePath), true);
if (isset($_REQUEST['SetContent'])) {
    unset($_POST['SetContent']);

    SetCurrentValueByDataPosition("CssIn", $_REQUEST['overwrite'], $_POST['CssIn']);
    SetCurrentValueByDataPosition("JavascriptIn", $_REQUEST['overwrite'], $_POST['JavascriptIn']);
    SetCurrentValueByDataPosition("CssList", $_REQUEST['overwrite'], $_POST['CssList']);
    SetCurrentValueByDataPosition("JavascriptList", $_REQUEST['overwrite'], $_POST['JavascriptList']);
    SetCurrentValueByDataPosition("CloakerPath", $_REQUEST['overwrite'], $_POST['CloakerPath']);
    SetCurrentValueByDataPosition("CloakerID", $_REQUEST['overwrite'], $_POST['CloakerID']);
    SetCurrentValueByDataPosition("EditMode", $_REQUEST['overwrite'], $_POST['EditMode']);
    SetCurrentValueByDataPosition("AffLink", $_REQUEST['overwrite'], $_POST['AffLink']);
    SetCurrentValueByDataPosition("VoluumCpid", $_REQUEST['overwrite'], $_POST['VoluumCpid']);
    unset($_POST['VoluumCpid']);
    unset($_POST['AffLink']);
    unset($_POST['EditMode']);
    unset($_POST['CssIn']);
    unset($_POST['JavascriptList']);
    unset($_POST['JavascriptIn']);
    unset($_POST['CssList']);
    unset($_POST['CloakerPath']);
    unset($_POST['overwrite']);
    unset($_POST['CloakerID']);
    file_put_contents($ConfigFilePath, json_encode($_POST));

    echo "Content Updated. Refresh to see changes.";
    exit;
}


$ConfigItems = array_keys((array)$data);

$CssIn = $overwritedata['CssIn'];
$JavascriptIn = $overwritedata['JavascriptIn'];
$CssList = $overwritedata['CssList'];
$JavascriptList = $overwritedata['JavascriptList'];
$CloakerPath = $overwritedata['CloakerPath'];
$CloakerID = $overwritedata['CloakerID'];
$EditorPassword = $overwritedata['EditMode'];
$AffLink = $overwritedata['AffLink'];
$VoluumCpid = $overwritedata['VoluumCpid'];


?>
<!DOCTYPE html>
<html>
<head>

    <link rel="stylesheet"
          href="/assets/css/main<?php if (isset($_REQUEST['variation'])) echo $_REQUEST['variation']; ?>.css"/>
</head>
<body>
<a href="ImportContent.php?overwrite=<?php echo $_REQUEST['overwrite']; ?>">Html Parser</a> - <a
        href="SetStructure.php?overwrite=<?php echo $_REQUEST['overwrite']; ?>">Structure Editor</a> - <a
        href="UploadImage.php?overwrite=<?php echo $_REQUEST['overwrite']; ?>">Image Uploader</a> - <a
        href="ClonePage.php?overwrite=<?php echo $_REQUEST['overwrite']; ?>">Page Creator</a> - <a
        href="SearchReplace.php?overwrite=<?php echo $_REQUEST['overwrite']; ?>">Search/replace </a>

<form method="post" enctype="multipart/form-data">

    <div id="TextNodes">
        <label for="VoluumCpid">Config Node (VoluumCpid)
            VoluumCpid
        </label>
        <input name="VoluumCpid" value="<?php echo $VoluumCpid ?>"/>
        <label for="EditMode">Config Node (AffLink)
            AffLink
        </label>
        <input name="AffLink" value="<?php echo $AffLink ?>"/>
        <label for="EditMode">Config Node (EditMode Password)
            EditMode
        </label>
        <input name="EditMode" value="<?php echo $EditorPassword ?>"/>
        <label for="CloakerPath">Config Node (CloakerPath)
            Absolute Path from webroot to dirty page aka

            /page2/page3/index.php
        </label>
        <input name="CloakerPath" value="<?php echo $CloakerPath ?>"/>
        <label for="CloakerID">Config Node (CloakerID)
            Cloaker ID
        </label>
        <input name="CloakerID" value="<?php echo $CloakerID ?>"/>
        <?php foreach ($ConfigItems as $Key) {
            $Text = $data->$Key;
            ?>
            <div>
                <label for="Text">Config Node (<?= $Key; ?>)
                    <button type="button" onclick="$(this).parent().parent().remove()">delete</button>
                    <button type="button" class="  primary" onclick="AddNode($(this).parent().parent())">Add Config Node
                    </button>
                </label>
                <textarea rows="<?php echo count(explode("\n", $Text)); ?>"
                          name="<?= $Key; ?>"><?php echo htmlentities($Text); ?></textarea>
            </div>
        <?php } ?>
        <button type="button" onclick="AddNode()">Add Config Node</button>
        <br/>

        <hr/>
        <label for="JavascriptIn">Config Node (JavascriptIn)

        </label>
        <textarea rows="<?php
        echo count(explode("\n", $JavascriptIn)); ?>"
                  name="JavascriptIn"><?php echo htmlentities($JavascriptIn); ?></textarea>

        <label for="JavascriptIn">Config Node (CssList)

        </label>

        <textarea rows="<?php
        echo count(explode("\n", $CssList)); ?>"
                  name="CssList"><?php echo htmlentities($CssList); ?></textarea>


        <label for="JavascriptIn">Config Node (JavascriptList)

        </label>
        <textarea rows="<?php
        echo count(explode("\n", $JavascriptList)); ?>"
                  name="JavascriptList"><?php echo htmlentities($JavascriptList); ?></textarea>


        <label for="CssIn">Config Node (CssIn)

        </label>
        <textarea rows="<?php
        echo count(explode("\n", $CssIn)); ?>"
                  name="CssIn"><?php echo htmlentities($CssIn); ?></textarea>


    </div>

    <br/>
    <input type="hidden" name="overwrite" value="<?php echo $_REQUEST['overwrite']; ?>"/>

    <button type="submit" class="fit" value="Set Content" name="SetContent">Set Content</button>
</form>
<script src="/assets/js/jquery.min.js"></script>
<script>

    function AddNode(node) {
        var New = prompt("Name Config Key")
        if (!New) {
            return;
        }
        if (node) {
            $(node).before($(` <div>
                <label for="Text">Config Node (${New})
                    <button type="button" onclick="$(this).parent().parent().remove()">delete</button>
                    <button type="button" class="  primary" onclick="AddNode($(this).parent().parent())">Add Config Node</button>
                </label>
                <textarea name="${New}"></textarea>
            </div>`
            ))
            return;
        }
        $('#TextNodes').append($(` <div>
                <label for="Text">Config Node (${New})
                    <button type="button" onclick="$(this).parent().parent().remove()">delete</button>
                    <button type="button" class="  primary" onclick="AddNode($(this).parent().parent())">Add Config Node</button>
                </label>
                <textarea name="${New}"></textarea>
            </div>`
        ))
    }

    function UpdateData(e) {
        e.preventDefault();
        $.ajax({

            type: "POST", // type of action POST || GET
            dataType: 'json', // data type
            data: $("form").serialize(), // post data || get data
            success: function (result) {
                // you can see the result from the console
                // tab of the developer tools
                alert(result);
            },
            error: function (xhr, resp, text) {
                alert("there was an error: " + text);
                console.log(xhr, resp, text);
            }
        })
    }
</script>
</body>
</html>