<template>
  <div>

    <md-button @click="editItem(null, null)" class="md-icon-button md-raised">
      <md-icon>add</md-icon>
    </md-button>

    <md-dialog :md-active.sync="editDialogShow">
      <md-dialog-title>{{ $t('Source') }}</md-dialog-title>
      <md-dialog-content>
        <div class="md-layout md-gutter">
          <div class="md-layout-item md-small-size-100">
            <md-field>
              <label for="type">{{$t('Type')}} <small>({{$t('twitter only')}})</small></label>
              <md-select v-model="form.type" md-dense :disabled="true" name="type" id="type">
                <md-option></md-option>
                <md-option value="TWITTER">Twitter</md-option>
              </md-select>
              <has-error class="md-error" :form="form" field="type"/>
            </md-field>
          </div>
        </div>
        <div class="md-layout md-gutter">
          <div class="md-layout-item md-small-size-100">
            <md-field>
              <label for="name">{{$t('Name')}}</label>
              <md-input v-model="form.name" :disabled="form.busy" name="name" id="name" autocomplete="given-name"/>
              <has-error :form="form" class="md-error" field="name"/>
            </md-field>
          </div>
        </div>
        <div class="md-layout md-gutter">
          <div class="md-layout-item md-small-size-100">
            <md-field>
              <label for="account_name">{{$t('Twitter name')}}</label>
              <md-input v-model="form.account_name" :disabled="form.busy"
                        name="account_name" id="account_name" autocomplete="given-account_name" />
              <has-error :form="form" class="md-error" field="account_name"/>
            </md-field>
          </div>
        </div>
      </md-dialog-content>
      <md-dialog-actions>
        <md-button class="md-primary" @click="editDialogShow = false">Close</md-button>
        <md-button class="md-primary" @click="saveItem">Save</md-button>
      </md-dialog-actions>
    </md-dialog>

    <md-list class="md-triple-line box-shadow">
      <div v-for="(item, index) in list" :key="item.id">
        <md-list-item>

          <md-avatar>
            <img src="/twitter.png" alt="Twitter">
          </md-avatar>

          <div class="md-list-item-text">
            <span>{{ item.name }} ({{ item.account_name }})</span>
            <span>{{ $t('Synchronized') }}: {{ item.synchronized_at }}</span>
            <p v-if="item.filter_type">{{ $t('Whitelist') }}: {{ item.filter_words }}</p>
            <p v-else>{{ $t('Blacklist') }}: {{ item.filter_words }}</p>
          </div>

          <div>
            <md-button @click="changeStatus(item, index)" class="md-icon-button md-list-action">
              <md-icon v-if="item.enabled" class="color-green">check</md-icon>
              <md-icon v-else class="color-grey">block</md-icon>
            </md-button>
          </div>
          <div>
            <md-button @click="editItem(item, index)"
                       class="md-icon-button md-list-action">
              <md-icon class="md-primary">edit</md-icon>
            </md-button>
          </div>
          <div>
            <md-button @click="deleteItem(item.id)" class="md-icon-button md-list-action">
              <md-icon class="md-accent">delete</md-icon>
            </md-button>
          </div>
        </md-list-item>

        <md-divider v-if="index < listCount - 1" class="md-inset"></md-divider>
      </div>
    </md-list>

  </div>

</template>

<script>
  import Form from 'vform'
  import axios from 'axios'

  export default {
    middleware: 'auth',

    head () {
      return { title: this.$t('Sources') }
    },

    data () {
      return {
        list: [],
        listCount: 0,
        editDialogShow: false,
        editDialogItemIndex: null,
        form: new Form({
          'type': 'TWITTER',
          'name': '',
          'account_name': ''
        })
      }
    },

    asyncData () {
      return axios.get('/sources')
        .then((response) => {
          let list = [];
          if (response.data.success) {
            list = response.data.list;
          }

          return {
            list: list,
            listCount: list.length
          }
        })
    },

    methods: {
      editItem (item, index) {
        this.form.clear();
        this.form.reset();
        if (item) {
          this.form.type = item.type;
          this.form.name = item.name;
          this.form.account_name = item.account_name;
        }
        this.editDialogItemIndex = index;
        this.editDialogShow = true;
      },

      saveItem () {
        let url;
        const isNew = this.editDialogItemIndex === null;
        if (isNew) {
          url = '/sources/add';
        } else {
          const id = this.list[this.editDialogItemIndex].id;
          url = '/sources/' + id + '/save';
        }

        this.form.post(url)
          .then(response => {
            this.form.reset();
            this.form.clear();
            this.editDialogShow = false;
            if (isNew) {
              this.list = response.data.list;
              this.listCount = this.list.length;
            } else {
              this.$set(this.list, this.editDialogItemIndex, response.data.item);
            }
          })
          .catch(error => {
            this.$toast.error(error.response.statusText);
          })
      },

      changeStatus (item, index) {
        axios.post('/sources/' + item.id + '/status', {
          enabled: !item.enabled
        })
          .then(response => {
            this.$set(this.list, index, response.data.item);
          })
          .catch(error => {
            this.$toast.error(error.response.statusText);
          })
      },

      openItem (item, index) {
        if (item.viewed) {
          return;
        }

        axios.post('/sources/' + item.id + '/save')
          .then(response => {
            this.$set(this.list, index, response.data.item);
          })
          .catch(error => {
            this.$toast.error(error.response.statusText)
          })
      },

      deleteItem (id) {
        // TODO: confirmation prompt
        axios.post('/sources/' + id + '/delete')
          .then(response => {
            this.list = response.data.list;
            this.listCount = this.list.length;
          })
          .catch(error => {
            this.$toast.error(error.response.statusText)
          })
      }
    }
  }
</script>

<style scoped>

</style>