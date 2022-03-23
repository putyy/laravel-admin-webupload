<style>
    .kit-image {
        height: 15rem;
        width: 15rem;
        position: relative;
    }

    .choose-image {
        position: absolute;
        top: 1rem;
        z-index: 5;
        opacity: 0;
        height: 15rem;
        width: 15rem;
    }

    .kit-video {
        width: 27rem;
        height: auto;
    }
</style>
<script src="/vendor/putyy/laravel-admin-webupload/rewriteSubmit.js"></script>
<script type="application/javascript">
    $(function () {
        rewriteSubmitApp.main("{{$form_url}}", "{{$scene_url}}");
    })
</script>
