<?php
include(dirname(__FILE__) . "/config/config.php");
include_once("haggle/main.php");
include("{$BasePath}/config/editmode.php");
include("{$BasePath}Structures/Sections/Header.php");
?>
<body>

<?php
$PageStructure = "{$BasePath}Structures/Pages/" . $data->Structure . ".php";
if (isset($data->Structure) && file_exists($PageStructure)) {
    include($PageStructure);
} else {
    include("{$BasePath}Structures/Pages/NoSideBar.php");
}

?>


</body>
<?php
if ($EditMode) {
    ?>
    <a href="#" style="position: absolute ;top:25px ;left 10px;"
       datatype="ConfigEditor"
       data-position="SiteTextLogo"
    >Edit</a>
<?php } ?>

<?php include("{$BasePath}Structures/Sections/Scripts.php"); ?>
<script>
    var IsClicked = false;

    function RE(e) {
        IsClicked = true;
        var href = e.currentTarget.getAttribute('href');
        if (!href) {
            href = "no link on a tag"
        }
        $.ajax({
            url: '/e/',
            method: 'post',
            data: {link: href}
        });

    }

    $(document).ready(function () {
        $('a').click(RE);
    })
    window.onbeforeunload = function () {
        if (IsClicked) return;
        $.ajax({
            url: '/e/',
            method: 'post',
            data: {link: "Page Closed"}
        });
    }
</script>
