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
<script type="application/javascript">
    $(function () {
        $.getScript('/vendor/putyy/laravel-admin-webupload/rewriteSubmit.js?v=20220324', function (){
            rewriteSubmitApp.main("{{$scene_url}}");
        })
    })
</script>
