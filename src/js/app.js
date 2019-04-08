import $ from 'jquery';
import webFontsLoader from 'webfontloader/webfontloader';
// import whatInput from 'what-input';
// import motionUI from 'motion-ui';
// import Foundation from 'foundation-sites';
// If you want to pick and choose which modules to include, comment out the above and uncomment
// the line below
// import './lib/foundation-explicit-pieces';

window.$ = $;

webFontsLoader.load({
    google: {
        families: []
    }
});
