import './bootstrap';

import Alpine from 'alpinejs';
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import { initializeRtlIconSpacing } from './rtl-icon-spacing';

window.Alpine = Alpine;
window.Swiper = Swiper;
window.SwiperNavigation = Navigation;
window.SwiperPagination = Pagination;
window.SwiperAutoplay = Autoplay;

initializeRtlIconSpacing();
Alpine.start();
