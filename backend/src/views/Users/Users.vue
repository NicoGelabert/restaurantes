<template>
  <div class="flex flex-col md:flex-row justify-between mb-3 w-full gap-4">
    <h1 class="text-3xl font-semibold">Usuarios</h1>
    <button type="button"
            @click="showAddNewModal()"
            class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-black hover:text-black hover:bg-white focus:outline-none"
    >
      Nuevo Usuario
    </button>
  </div>
  <UsersTable @clickEdit="editUser"/>
  <UserModal v-model="showUserModal" :user="userModel" @close="onModalClose"/>
</template>

<script setup>
import {computed, onMounted, ref} from "vue";
import store from "../../store";
import UserModal from "./UserModal.vue";
import UsersTable from "./UsersTable.vue";

const DEFAULT_USER = {
  id: '',
  name: '',
  email: '',
}

const users = computed(() => store.state.users);

const userModel = ref({...DEFAULT_USER})
const showUserModal = ref(false);

function showAddNewModal() {
  showUserModal.value = true
}

function editUser(u) {
    userModel.value = u;
    showAddNewModal();
}

function onModalClose() {
  userModel.value = {...DEFAULT_USER}
}
</script>

<style scoped>

</style>
