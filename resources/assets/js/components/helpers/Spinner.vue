<template>
    <div :class="`preloader-wrapper small ${this.size} active`">
        <div class="spinner-layer spinner-red-only">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div><div class="gap-patch">
            <div class="circle"></div>
        </div><div class="circle-clipper right">
            <div class="circle"></div>
        </div>
        </div>
    </div>
</template>

<script>
    export default {

        props: [
            'size'
        ],

    }
</script>
