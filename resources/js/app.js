import './theme';
import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import { createShareTools, formShareModal } from './share-tools';
import { initPublicFormValidation } from './public-form';

window.Alpine = Alpine;
window.Sortable = Sortable;
window.loadQrCode = () => import('qrcode').then((module) => module.default);
window.createShareTools = createShareTools;
window.formShareModal = formShareModal;

Alpine.start();
initPublicFormValidation();
