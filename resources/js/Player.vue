<template>
    <div class="m-1 p-3 bg-dark rounded">
        <template v-if="player">
            <p class="text-center">
                Player {{player.player_id}} {{ player.stack }}:
                <span v-if="player.is_dealer" v-bind:class="'bg-primary'" class="d-inline rounded p-1"><strong>D</strong></span>
                <span v-else-if="player.big_blind" v-bind:class="'bg-primary'" class="d-inline rounded p-1"><strong>BB</strong></span>
                <span v-else-if="player.small_blind" v-bind:class="'bg-primary'" class="d-inline rounded p-1"><strong>SB</strong></span>
                <span v-if="player.action_id" v-bind:class="actionColours[player.action_name]" class="d-inline rounded p-1"><strong>{{player.action_name}}</strong></span>
            </p>

            <div v-show="isActive()" class="row mb-2 m-0 p-0 justify-content-center">
                <div v-for="card in player.whole_cards" class="m-0 me-1 bg-white" v-bind:class="$root.suitColours[card.suit]" style="width:100px;height:130px;">
                    <div class="card-body ps-1 pe-0">
                        <p class="fs-2"><strong>{{card.rank}}</strong> {{card.suitAbbreviation}}</p>
                    </div>
                </div>
            </div>

            <div v-show="showOptions(player.action_on)" class="row">
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button v-on:click="action(option, player)" class="btn btn-primary me-1" v-for="option in player.availableOptions" :key="option.name" v-bind:data-action-id="option.id">
                        {{option.name}}
                    </button>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    name: "Player",
    data() {
        return {
            errors: {},
            loading: false,
            actionColours: {
                "Fold": [
                    "bg-info"
                ],
                "Check": [
                    "bg-info"
                ],
                "Call": [
                    "bg-success"
                ],
                "Bet": [
                    "bg-warning"
                ],
                "Raise": [
                    "bg-danger"
                ]
            },
        }
    },
    props: {
        player: {
            type: Object,
            default: null
        },
        winner: {
            type: [Object, Boolean],
            default: false
        }
    },
    methods: {
        showOptions(action_on){
            return action_on === true && this.winner === false;
        },
        isActive(){
            return this.player.active;
        },
        action(action, player){
            this.$root.$emit('action', action, player);
        }
    },
}
</script>

<style scoped>
</style>
