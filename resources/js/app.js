/**
 * Vue does no longer provide a default export, and instead uses named exports. 
 * 
 * Added 'import * as' to resolve.
 */
import * as Vue from 'vue';

const app = new Vue([]);

console.log('hello');
