<?php
function generate_mathjax_script() {
    ob_start(); ?>
<script type="text/x-mathjax-config">
    MathJax.Hub.Config({
        extensions: ["tex2jax.js"],
        jax: ["input/TeX", "output/HTML-CSS"],
        tex2jax: {
            inlineMath: [ ['<?php echo MATHJAX_INLINE_BEGIN ?>','<?php echo MATHJAX_INLINE_END ?>'] ],
            displayMath: [ ["<?php echo MATHJAX_TEX_BEGIN ?>","<?php echo MATHJAX_TEX_END ?>"] ],
            processEscapes: true
        },
        "HTML-CSS": { availableFonts: ["TeX"], webFont: "TeX", imageFont: null }
    });
</script>
<!--<script type="text/javascript"  src="//cdn.bootcss.com/mathjax/2.6.1/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>-->
<script async type="text/javascript" src="../assets/Mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
    <?php
    return ob_get_clean();
}

