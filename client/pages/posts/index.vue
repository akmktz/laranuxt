<template>
  <div>
    <md-card v-for="(item, index) in list" :key="item.id" md-with-hover class="margin-bottom">
      <md-ripple>
        <transition name="slide-fade">
          <md-card-header v-bind:class="{ 'small-paddings': openedItemId === item.id }" class="d-flex justify-content-between">
            <div @click="openItem(item, index)">
                <div v-bind:class="{
                  'font-weight-bold': !item.viewed,
                  'md-title': openedItemId !== item.id,
                  'md-title-small': openedItemId === item.id
                   }"
                     class="">{{ item.title }}</div>
                <div class="md-subhead">{{item.original_date}}</div>
            </div>
            <md-button v-if="openedItemId !== item.id" @click="deleteItem(item.id)" class="md-icon-button md-accent align-self-end">
              <md-icon>delete</md-icon>
            </md-button>
          </md-card-header>
        </transition>
        <transition name="slide-fade">
          <div v-if="openedItemId === item.id">
            <md-card-content>
              <span v-html="item.body"></span>
            </md-card-content>

            <md-card-actions>
              <md-button @click="deleteItem(item.id)"><md-icon class="md-accent">delete</md-icon></md-button>
            </md-card-actions>
          </div>
        </transition>
      </md-ripple>
    </md-card>
  </div>
</template>

<script>
    import axios from 'axios';

    export default {
    middleware: 'auth',

    head () {
      return { title: this.$t('Posts') }
    },

    data () {
      return {
        list: [],
        openedItemId: null
      };
    },

    asyncData () {
      return axios.get('/posts')
        .then((response) => {
          let list = [];
          if (response.data.success) {
            list = response.data.list;
          }

          return { list: list };
        });
    },

    mounted () {
      if (process.env.wsEnabled && process.env.wsUrl) {
        const ws = new WebSocket(process.env.wsUrl);
        ws.onmessage = ({data}) => {
          if (data === 'UPDATED') {
            this.getList();
          }
        };
      }
    },

    methods: {
      getList () {
        return axios.get('/posts')
          .then((response) => {
            if (response.data.success) {
              this.list = response.data.list;
              this.$toast.show('Updated');
            }
          });
      },

      openItem (item, index) {
        this.openedItemId = item.id;

        // Run twiier script for build widget
        let twitterScript = document.getElementById('widget-twitter-script');
        if (twitterScript) {
          twitterScript.remove();
        }
        twitterScript = document.createElement('script');
        twitterScript.id = 'widget-twitter-script';
        twitterScript.src = 'https://platform.twitter.com/widgets.js';
        document.head.appendChild(twitterScript);

        if (item.viewed) {
          return;
        }

        axios.post('/posts/' + item.id + '/viewed')
          .then(response => {
            this.$set(this.list, index, response.data.item);
          })
          .catch(error => {
            this.$toast.error(error.response.statusText)
          })
      },

      deleteItem (id) {
        this.openedItemId = null;

        axios.post('/posts/' + id + '/delete')
          .then(response => {
            this.list = response.data.list;
          })
          .catch(error => {
            this.$toast.error(error.response.statusText)
          })
      }
    }
  }
</script>

<style scoped>
  .margin-bottom {
    margin-bottom: 10px;
  }
  .small-paddings {
    margin-bottom: 0 !important;
    padding: 10px 16px 0px 16px !important;
  }
  .md-title-small {
    max-height: 1.5em;
    overflow: hidden;
    padding-bottom: 0;
  }

  .slide-fade-enter-active {
    transition: all .3s ease;
  }
  .slide-fade-leave-active {
    transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
  }
  .slide-fade-enter, .slide-fade-leave-to
    /* .slide-fade-leave-active до версии 2.1.8 */ {
    transform: translateY(-10px);
    opacity: 0;
  }
</style>