import './bootstrap';

import Alpine from 'alpinejs';
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';

window.Alpine = Alpine;
window.Swiper = Swiper;
window.SwiperNavigation = Navigation;
window.SwiperPagination = Pagination;
window.SwiperAutoplay = Autoplay;

Alpine.start();
