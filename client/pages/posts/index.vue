<template>
  <div>
    <div v-for="(item, index) in list" :key="item.id" class="card margin-bottom">
      <div @click="openItem(item, index)" class="card-header">
        <span v-bind:class="{ 'font-weight-bold': !item.viewed }">
         <span>{{ item.title }}</span> <span class="float-right">  {{item.created_at}}</span>
        </span>
      </div>
      <transition name="slide-fade">
        <div v-if="openedItemId === item.id" class="card-body">
          <span v-html="item.body"></span>
          <button @click="deleteItem(item.id)" class="btn btn-sm btn-danger float-right">{{ $t('delete') }}</button>
        </div>
      </transition>
    </div>

  </div>

</template>

<script>

  import axios from 'axios'

  export default {
  middleware: 'auth',

  head () {
    return { title: this.$t('Posts') }
  },

  data () {
    return {
      list: [],
      openedItemId: null
    }
  },

  asyncData () {
    return axios.get('/posts')
      .then((response) => {
        let list = [];
        if (response.data.success) {
          list = response.data.list;
        }

        return { list: list }
      })
  },

  methods: {
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
</style>