import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */


// assets/app.js
import { reloadOnBack } from './js/reloadOnBack';
import './js/hamburger.js';
import './js/hamburgerHome.js';
import './js/tagfilter.js';
import './js/filterUserAdministrator.js';
import './js/filterOrderAdministrator.js';



reloadOnBack();
