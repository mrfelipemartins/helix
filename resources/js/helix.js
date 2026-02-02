import jsnview from "jsnview/dist/index.js";
import Chart from "chart.js/auto";
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import visualize from "./visualize";

window.Jsnview = jsnview;
window.Chart = Chart;

Alpine.data('visualize', visualize);

Livewire.start()