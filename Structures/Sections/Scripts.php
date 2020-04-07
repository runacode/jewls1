<!-- Scripts -->

<script src="/assets/js/jquery.min.js"></script>
<?php include_once("{$BasePath}pixel.php");
if($EditMode){
    include_once ("{$BasePath}config/Editor/Editor.php");
}
?>

<?php
if(isset($data->JavascriptList)){
    ?>

        <?php echo $data->JavascriptList; ?>

    <?php

}

?>
<?php
if(isset($data->JavascriptIn) && !$EditMode){
    ?>
    <script>
        <?php echo $data->JavascriptIn; ?>
    </script>
    <?php

}

?>
</html>
