import './bootstrap';
import './../../vendor/power-components/livewire-powergrid/dist/powergrid';

import flatpickr from "flatpickr";
import { Portuguese } from "flatpickr/dist/l10n/pt.js";

// Torna o flatpickr global para o PowerGrid acessar
window.flatpickr = flatpickr;

// Define o padrão global como PT
flatpickr.localize(Portuguese);
