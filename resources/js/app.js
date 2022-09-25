import { createApp } from 'vue'
import axios from 'axios';
import Player from './Player.vue'

// axios.post('/index.php/action', {
//     'player_id': 1
// }).then(response => {
//     console.log(response);
// }).catch(error => {
//     console.log(error);
// });

createApp({
    components: {
        Player
    },
    data() {
        return {
			deck: false,
			pot: 0,
			players: false,
			communityCards: false,
			winner: false,
			errors: {},
			loading: false,
			suitColours: {
				"Clubs": [
					"text-dark",
					"border border-2 border-dark"
				],
				"Diamonds": [
					"text-danger",
					"border border-2 border-danger"
				],
				"Hearts": [
					"text-danger",
					"border border-2 border-danger"
				],
				"Spades": [
					"text-dark",
					"border border-2 border-dark"
				]
			},
            actionBetAmounts: {
                "Fold": null,
                "Check": null,
                "Call": 50.0,
                "Bet": 50.0,
                "Raise": 50.0
            }
		}
    },
    methods: {
		action(action, player){

			console.log('root');
			let active = 1;
			if(action.id === 1){
				active = 0;
			}

			let payload = {
				deck:           this.deck,
				player_id:      player.player_id,
				action_id:      action.id,
				table_seat_id:  player.table_seat_id,
				hand_street_id: player.hand_street_id,
				active:         active,
				bet_amount:     this.actionBetAmounts[action.name]
			};

			this.loading = true

			axios.post('/index.php/action', payload).then(response => {

				console.log(response.data);

                let data = response.data.body;

				this.loading        = false
				this.players        = data.players;
				this.communityCards = data.communityCards;
				this.deck           = data.deck;
				this.winner         = data.winner ? data.winner : false;
                this.pot            = data.pot;


			}).catch(error => {

				console.log(error);

				this.loading = false
				this.errors = error.response.data.errors

			});
		},
		gameData(){
			axios.post('/index.php/play').then(response => {

                console.log(response.data);

                let data = response.data.body;

				this.winner         = false;
				this.players        = data.players;
				this.communityCards = data.communityCards;
				this.deck           = data.deck;
				this.pot            = data.pot;

			});
		}
	},
    mounted() {
        this.gameData();
    }
}).mount('#app');
