//import Vue from 'vue';
import './styles/vuePage.css';
import './bootstrap';

require('bootstrap')
import { createApp, compile } from 'vue';
import Users from './pages/users';


 createApp(Users).mount('#users')