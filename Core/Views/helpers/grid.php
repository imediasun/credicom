<table id="grid"></table>
<div id="gridPager"></div>
<script type="text/javascript">
    $(function () {
        <?= $gridConfig ?>
    });

    function gridPanelOpenPage(options, event) {
        gridOpenPage({
            options: options,
            rowid: null,
        });
    }

    function gridOpenPage(action) {
        var url = action.options.url.replace('<rowId>', action.rowid);
        var target = action.options.target || '_self';

        window.open(url, target);
    }
</script>