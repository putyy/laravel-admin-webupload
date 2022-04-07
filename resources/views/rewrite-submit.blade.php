<style>
    .kit-file {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .kit-file img {
        height: 15rem;
        width: 15rem;
        position: relative;
    }

    .kit-file video {
        height: auto;
        width: 20rem;
        display: block;
        vertical-align: initial;
    }

    .kit-file .file-name {
        width: 20rem;
        height: 5rem;
        line-height: 5rem;
        overflow: scroll;
    }

    .kit-file .option-icon {
        color: #e3b041;
        z-index: 1;
        right: 0.3rem;
        text-align: right;
        position: absolute;
    }

    .kit-file .option-icon>span:hover{
        color: red;
    }

    .kit-item {
        border: 1px solid #ccc;
        position: relative;
        line-height: 50%;
        padding: 0.3rem;
        margin: 0.2rem;
    }

    .kit-item p {
        position: absolute;
        top: 1px;
        z-index: 1;
        color: #e3b041;
        max-width: 8rem;
        overflow: scroll;
        line-height: normal;
        white-space: nowrap;
        left: 0.4rem;
    }
    .kit-item p::-webkit-scrollbar {
        display: none;
    }

    .choose-image {
        position: absolute;
        top: 1rem;
        z-index: 5;
        opacity: 0;
        height: 15rem;
        width: 15rem;
    }
</style>
<script type="application/javascript">
    $(function () {
        $.getScript('/vendor/putyy/laravel-admin-webupload/rewriteSubmit.js?v=11', function (){
            rewriteSubmitApp.main("{{$scene_url}}");
        })
    })
</script>
