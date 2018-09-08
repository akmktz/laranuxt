<template>
  <div>
    <card v-for="item in list" :key="item.id" :title="item.created_at + ' ' + item.title" class="margin-bottom">
      <span v-html="item.body"></span>
    </card>
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
      list: []
    }
  },

  asyncData () {
    return axios.get('/posts')
      .then((result) => {
        let list = [];
        if (result.data.success) {
          list = result.data.list;
        }

        return { list: list }
      })
  }

}
</script>

<style scoped>
  .margin-bottom {
    margin-bottom: 10px;
  }
</style>