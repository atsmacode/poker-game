import { createApp } from 'vue'
import axios from 'axios';

axios.post('/index.php/action', {
    'player_id': 1
}).then(response => {
    console.log(response);
}).catch(error => {
    console.log(error);
});

/**
 * Vue3 initialization:
 */
createApp().mount('#app');
