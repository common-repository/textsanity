<style>
.txsy_style.txsy_chat .txsy_chat_popup {
    color: <?php echo esc_html($setting['text_color']); ?>;
    background: <?php echo esc_html($setting['background_color']); ?>;
}
.txsy_style .txsy_chat_open {
    color: <?php echo esc_html($setting['text_color']); ?>;
    background: <?php echo esc_html($setting['background_color']); ?>;
}
.txsy_style .txsy_chat_open svg {
    fill: <?php echo esc_html($setting['text_color']); ?> !important;
}
.txsy_style .txsy_chat_open svg * {
    fill: <?php echo esc_html($setting['text_color']); ?> !important;
}
.txsy_style .txsy_chat_open:hover {
    color: <?php echo esc_html($setting['text_color']); ?>;
    background: <?php echo esc_html($setting['background_color']); ?>;
}
.txsy_style.txsy_chat .txsy_chat_close {
    color: <?php echo esc_html($setting['text_color']); ?>;
    background: <?php echo esc_html($setting['background_color']); ?>;
}
.txsy_style .txsy_chat_close:hover {
    color: <?php echo esc_html($setting['text_color']); ?>;
    background: <?php echo esc_html($setting['background_color']); ?>;
}
</style>
<div class="<?php echo $this->key . $setting['style_class'] . ' ' . $this->key_ . 'chat'; ?> txsy_right">
    <div class="<?php echo esc_attr($this->key_ . 'chat_popup'); ?>">
        <header class="txsy_interior">
        <?php if($setting['description']): ?>
            <p><?php echo esc_html($setting['description']); ?></p>
        <?php endif; ?>
        </header>
        <div class="txsy_chat_form">
            <form class="<?php echo esc_attr($this->key_ . 'ajax_form'); ?>" method="POST">
                <input type="hidden" name="action" value="<?php echo esc_attr($this->key_ . 'front'); ?>" />
                <input type="hidden" name="type" value="chat" />
                <input type="hidden" name="widget_id" value="<?php echo esc_attr($setting['widget_id']); ?>" />
                <div class="txsy_flex">
                    <label>Phone Number:</label>
                    <input type="text" name="phone" value="" />
                    <p class="txsy_submit">
                        <button><span class="dashicons dashicons-update"></span><span class="dashicons dashicons-yes"></span> Submit</button>
                    </p>
                </div>
            </form>
            <p class="txsy_src"><a href="<?php echo esc_url($this->textsanity_url); ?>">Powered By <img src="<?php echo esc_url($logo_url); ?>" alt="TextSanity Logo" /></a></p>
        </div>
    </div>
    <!--
    <button class="<?php echo esc_attr($this->key_ . 'chat_open'); ?>" title="Text Form"><span class="dashicons dashicons-admin-comments"></span></button>
    -->

    <button class="<?php echo esc_attr($this->key_ . 'chat_open'); ?>" title="Text Form">


        <svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" version="1.1" id="svg2" xml:space="preserve" width="480" height="480" viewBox="0 0 480 480" sodipodi:docname="TextSanity_Brand.ai"><metadata id="metadata8"><rdf:RDF><cc:Work rdf:about=""><dc:format>image/svg+xml</dc:format><dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage" /></cc:Work></rdf:RDF></metadata><defs id="defs6"><clipPath clipPathUnits="userSpaceOnUse" id="clipPath22"><path d="M 0,360 H 360 V 0 H 0 Z" id="path20" /></clipPath></defs><sodipodi:namedview pagecolor="#ffffff" bordercolor="#666666" borderopacity="1" objecttolerance="10" gridtolerance="10" guidetolerance="10" inkscape:pageopacity="0" inkscape:pageshadow="2" inkscape:window-width="640" inkscape:window-height="480" id="namedview4" /><g id="g10" inkscape:groupmode="layer" inkscape:label="TextSanity_Brand" transform="matrix(1.3333333,0,0,-1.3333333,0,480)"><g id="g12" transform="translate(112.3684,256.2823)"><path d="m 0,0 h -20.85 l 2.607,12.286 H 37.231 L 34.625,0 H 14.706 L 3.351,-54.172 h -14.706 z" style="fill:#000000;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path14" /></g><g id="g16"><g id="g18" clip-path="url(#clipPath22)"><g id="g24" transform="translate(169.8918,231.3369)"><path d="M 0,0 C 0,4.934 -1.21,10.238 -8.936,10.238 -16.382,10.238 -20.105,5.398 -22.06,0 Z m -23.456,-8.377 c -0.093,-1.21 -0.093,-2.048 -0.093,-2.792 0,-5.585 3.444,-9.401 10.611,-9.401 5.305,0 7.912,3.537 9.866,6.608 H 10.146 C 5.957,-24.759 -0.931,-30.529 -14.8,-30.529 c -12.845,0 -21.408,7.352 -21.408,21.035 0,15.358 10.146,29.692 26.527,29.692 13.404,0 22.433,-7.074 22.433,-21.036 0,-2.605 -0.279,-5.212 -0.745,-7.539 z" style="fill:#000000;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path26" /></g><g id="g28" transform="translate(214.2903,227.1485)"><path d="M 0,0 12.193,-25.038 H -1.769 L -8.75,-9.215 -21.595,-25.038 H -36.58 l 22.339,25.783 -10.891,22.339 h 14.149 L -5.678,9.215 5.212,23.084 h 14.8 z" style="fill:#000000;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path30" /></g><g id="g32" transform="translate(259.6204,250.2325)"><path d="M 0,0 H 9.494 L 7.446,-8.843 h -9.401 l -4.933,-23.921 c -0.186,-1.024 -0.372,-1.862 -0.372,-2.234 0,-3.444 2.513,-3.537 4.467,-3.537 1.583,0 3.165,0.093 4.747,0.279 l -2.233,-10.238 c -2.7,-0.279 -5.492,-0.466 -8.284,-0.466 -6.144,0 -12.752,1.955 -12.566,9.773 0,1.21 0.279,2.886 0.651,4.561 l 5.399,25.783 h -8.656 L -21.687,0 h 8.47 l 2.979,14.613 H 2.979 Z" style="fill:#000000;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path34" /></g><g id="g36" transform="translate(121.303,180.0938)"><path d="m 0,0 c -0.08,10.048 -5.386,13.264 -15.192,13.264 -7.074,0 -14.629,-3.296 -14.629,-11.495 0,-6.11 4.822,-8.039 9.645,-9.486 l 6.11,-1.848 c 7.877,-2.412 15.433,-4.984 15.433,-14.63 0,-5.627 -3.537,-17.685 -22.186,-17.685 -12.862,0 -22.266,6.19 -21.623,20.096 h 5.466 c -0.562,-11.173 6.109,-15.434 16.559,-15.434 7.476,0 16.318,3.698 16.318,12.299 0,8.119 -7.878,9.565 -14.228,11.415 l -5.627,1.688 c -6.672,1.929 -11.334,5.948 -11.334,13.343 0,11.495 10.129,16.399 20.337,16.399 11.656,0 21.06,-4.662 20.418,-17.926 z" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path38" /></g><g id="g40" transform="translate(157.553,161.6055)"><path d="m 0,0 -0.161,0.161 c -1.608,-2.01 -6.591,-2.171 -9.163,-2.331 -6.19,-0.482 -16.399,-0.804 -16.399,-9.485 0,-5.306 4.421,-7.476 9.244,-7.476 8.119,0 13.263,4.903 14.872,12.138 z m -25.08,6.913 c 1.367,9.405 8.601,13.585 17.524,13.585 5.626,0 14.388,-1.93 14.388,-9.244 C 6.832,7.234 5.064,0.965 4.26,-2.732 2.411,-12.058 1.527,-14.228 1.527,-16.72 c 0,-1.446 1.447,-1.687 2.653,-1.687 0.723,0 1.286,0.08 2.009,0.16 l -0.723,-4.019 c -1.125,-0.241 -2.814,-0.402 -4.18,-0.402 -2.813,0 -4.341,1.769 -4.421,4.502 0,0.723 0.08,1.527 0.16,2.25 l -0.16,0.161 c -2.894,-4.823 -8.601,-7.637 -14.388,-7.637 -7.557,0 -13.264,3.376 -13.264,11.496 0,8.761 6.993,11.575 14.389,12.861 5.305,0.804 9.967,0.402 13.182,1.206 3.296,0.804 4.985,2.652 4.985,8.118 0,4.903 -4.904,5.948 -8.762,5.948 -6.431,0 -11.978,-2.411 -13.023,-9.324 z" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path42" /></g><g id="g44" transform="translate(176.1194,180.8975)"><path d="M 0,0 H 4.662 L 3.216,-7.556 h 0.16 c 3.136,4.904 8.602,8.762 14.791,8.762 7.234,0 12.54,-3.216 12.54,-11.173 0,-1.287 -0.241,-2.974 -0.644,-4.904 l -5.707,-26.607 h -5.064 l 5.788,26.768 c 0.321,1.286 0.563,2.814 0.563,4.18 0,5.467 -4.18,7.475 -8.682,7.475 -7.315,0 -13.986,-6.591 -16.237,-17.121 l -4.582,-21.302 h -5.064 z" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path46" /></g><g id="g48" transform="translate(221.6135,196.8135)"><path d="M 0,0 H 5.063 L 3.296,-8.118 h -5.065 z m -3.457,-15.916 h 5.064 l -8.922,-41.478 h -5.064 z" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path50" /></g><g id="g52" transform="translate(227.3196,180.8975)"><path d="m 0,0 h 7.477 l 2.652,12.54 h 5.064 L 12.54,0 h 8.279 L 20.016,-4.26 H 11.575 L 5.868,-30.867 c -0.401,-1.929 -0.562,-2.653 -0.562,-3.939 0,-1.447 0.723,-2.893 2.652,-2.893 2.01,0 3.939,0.16 5.948,0.482 l -0.884,-4.422 c -1.688,-0.16 -3.456,-0.321 -5.144,-0.321 -3.778,0 -7.637,0.965 -7.637,5.948 0,0.885 0.161,2.412 0.564,4.261 L 6.512,-4.26 h -7.476 z" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path54" /></g><g id="g56" transform="translate(243.0735,128.5684)"><path d="m 0,0 c 1.446,-0.161 2.894,-0.241 4.34,-0.241 3.055,0 5.225,2.331 6.512,4.501 l 3.777,6.592 -7.636,41.477 h 5.305 l 5.948,-35.208 h 0.161 l 19.132,35.208 h 5.466 L 13.504,1.045 c -2.09,-3.698 -5.707,-5.547 -10.048,-5.547 -1.447,0 -2.975,0.322 -4.421,0.402 z" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path58" /></g><g id="g60" transform="translate(280.0388,313.5235)"><path d="m 0,0 c 6.52,0.111 13.142,-1.809 18.618,-5.48 5.493,-3.645 9.875,-8.976 12.372,-15.155 1.257,-3.082 2.038,-6.371 2.313,-9.693 0.089,-0.819 0.093,-1.687 0.126,-2.47 l 0.035,-2.028 0.07,-4.053 0.28,-16.217 1.167,-64.866 1.292,-64.866 0.093,-4.054 0.044,-2.041 c -0.007,-0.853 -0.002,-1.708 -0.034,-2.562 -0.141,-3.419 -0.679,-6.838 -1.6,-10.151 -1.835,-6.635 -5.206,-12.805 -9.691,-17.944 -8.902,-10.347 -22.31,-16.649 -36.179,-16.871 l 9.568,15.515 c 1.149,-2.237 2.138,-4.363 3.161,-6.512 l 3.062,-6.444 6.124,-12.885 8.291,-17.445 -19.187,2.21 -52.029,5.993 -5.015,0.578 -2.745,4.235 -12.537,19.338 8.981,-4.883 -73.199,1.14 -36.599,0.539 -9.15,0.135 -4.575,0.067 c -1.558,0.029 -2.9,0.016 -4.935,0.105 -7.523,0.371 -14.972,2.652 -21.387,6.557 -6.431,3.89 -11.831,9.372 -15.649,15.8 -1.888,3.223 -3.407,6.668 -4.465,10.249 -1.046,3.584 -1.657,7.29 -1.817,10.998 l -0.024,1.39 c -0.006,0.457 -0.016,0.96 -0.007,1.306 l 0.028,2.287 0.055,4.575 0.111,9.151 0.222,18.3 0.852,73.199 0.357,36.6 0.09,9.15 0.046,4.591 c 0.022,1.876 0.205,3.755 0.536,5.608 1.313,7.429 5.272,14.311 10.931,19.192 5.627,4.923 12.999,7.821 20.435,8.069 l 1.391,0.024 1.16,-0.01 2.288,-0.022 4.575,-0.045 9.15,-0.089 36.6,-0.358 c 24.4,-0.235 48.8,-0.521 73.2,-0.581 24.4,0.02 48.8,0.431 73.2,0.824 m 0,-11.323 c -24.4,0.393 -48.8,0.804 -73.2,0.824 -24.4,-0.061 -48.8,-0.346 -73.2,-0.581 l -36.6,-0.358 -9.15,-0.089 -4.575,-0.045 -2.288,-0.022 -1.126,-0.013 -0.896,-0.035 c -4.76,-0.263 -9.35,-2.186 -12.85,-5.374 -3.517,-3.163 -5.887,-7.52 -6.602,-12.131 -0.18,-1.154 -0.272,-2.322 -0.263,-3.496 l 0.043,-4.56 0.09,-9.15 0.358,-36.6 0.851,-73.199 0.222,-18.3 0.111,-9.151 0.055,-4.575 0.028,-2.287 c 10e-4,-0.418 0.02,-0.676 0.032,-0.981 l 0.034,-0.897 c 0.168,-2.383 0.62,-4.726 1.336,-6.982 0.73,-2.252 1.751,-4.403 2.99,-6.412 2.52,-4 6.022,-7.373 10.099,-9.695 4.072,-2.333 8.679,-3.61 13.347,-3.724 1.014,-0.023 2.723,0.004 4.214,0.024 l 4.575,0.065 9.15,0.129 36.601,0.513 73.201,1.09 5.76,0.086 3.222,-4.969 12.537,-19.339 -7.76,4.813 52.029,-5.992 -10.896,-15.234 -6.068,12.769 -3.035,6.385 c -1.011,2.126 -2.021,4.292 -3.038,6.264 l -8.274,16.055 17.842,-0.538 c 3.902,-0.118 7.845,0.6 11.524,2.106 3.683,1.492 7.065,3.807 9.82,6.689 2.771,2.874 4.871,6.337 6.102,10.072 0.619,1.868 1.025,3.806 1.205,5.783 l 0.093,1.489 0.048,2.014 0.092,4.054 1.292,64.866 1.167,64.866 0.28,16.217 0.071,4.053 0.035,2.028 c -0.011,0.566 0.019,1.052 -0.025,1.579 -0.094,2.069 -0.502,4.12 -1.216,6.07 -1.412,3.904 -4.061,7.405 -7.493,9.866 -3.426,2.478 -7.606,3.872 -11.901,3.96" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path62" /></g></g></g></g></svg>


</button>
    <button class="<?php echo esc_attr($this->key_ . 'chat_close'); ?>" title="Close"><span class="dashicons dashicons-no-alt"></span></button>
</div>
