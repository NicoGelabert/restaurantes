<!-- This example requires Tailwind CSS v2.0+ -->
<template>
    <div class="flex items-center justify-between mb-3">
      <h1 v-if="!loading" class="text-3xl font-semibold">
        {{ article.id ? `Editar: "${article.title}"` : 'Crear Nuevo Artículo' }}
      </h1>
    </div>
    <div class="bg-white rounded-lg shadow animate-fade-in-down">
      <Spinner v-if="loading"
                class="absolute left-0 top-0 bg-white right-0 bottom-0 flex items-center justify-center z-50"/>
      <form v-if="!loading" @submit.prevent="onSubmit">
        <div class="grid grid-cols-1 md:grid-cols-3">
          <div class="col-span-2 px-4 pt-5 pb-4">
            <div class="flex flex-col gap-2">
                <h3 class="text-lg font-bold">Título</h3>
                <CustomInput class="mb-2" v-model="article.title" label="Título del Artículo" :errors="errors['title']"/>
            </div>
            <hr class="my-4">
            <div class="flex flex-col gap-2">
                <h3 class="text-lg font-bold">Subtítulo</h3>
                <CustomInput class="mb-2" v-model="article.subtitle" label="Subtítulo" :errors="errors['subtitle']"/>
            </div>
            
            <hr class="my-4">
            <div class="flex flex-col gap-2">
                <h3 class="text-lg font-bold">Párrafo Principal</h3>
                <CustomInput class="mb-2" v-model="article.news_lead" label="Párrafo Principal" :errors="errors['news_lead']"/>
            </div>
            <hr class="my-4">
            <div class="flex flex-col gap-2">
              <h3 class="text-lg font-bold">Cuerpo de la Noticia</h3>
              <CustomInput type="richtext" class="mb-2" v-model="article.description" label="Description" :errors="errors['description']"/>
            </div>
            <hr class="my-4">
            <div class="flex flex-col gap-2">
              <h3 class="text-lg font-bold">Autor/es</h3>
              <treeselect v-model="article.authors" :multiple="true" :options="authorsOptions" :errors="errors['authors']"/>
            </div>
            <hr class="my-4">
            <div class="flex flex-col gap-2">
              <h3 class="text-lg font-bold">Publicar</h3>
              <CustomInput type="checkbox" class="mb-2" v-model="article.published" :label="article.published ? 'Publicado' : 'No publicado'" :errors="errors['published']"/>
            </div>
          </div>
          <div class="col-span-1 px-4 pt-5 pb-4">
            <image-preview v-model="article.images"
                            :images="article.images"
                            v-model:deleted-images="article.deleted_images"
                            v-model:image-positions="article.image_positions"/>
          </div>
        </div>
        <footer class="bg-gray-50 rounded-b-lg px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button type="submit"
                  class="bg-black text-base font-medium text-white border rounded-md border-gray-300 shadow-sm w-full inline-flex justify-center mt-3 px-4 py-2 hover:bg-black/10 hover:text-black focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-black sm:w-auto sm:mt-0 sm:ml-3 sm:text-sm">
            Guardar
          </button>
          <button type="button"
                  @click="onSubmit($event, true)"
                  class="bg-black text-base font-medium text-white border rounded-md border-gray-300 shadow-sm w-full inline-flex justify-center mt-3 px-4 py-2 hover:bg-black/10 hover:text-black focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-black sm:w-auto sm:mt-0 sm:ml-3 sm:text-sm">
            Guardar y Cerrar
          </button>
          <router-link :to="{name: 'app.articles'}"
                        class="bg-white text-base font-medium text-gray-700 border rounded-md border-gray-300 shadow-sm w-full inline-flex justify-center mt-3 px-4 py-2 hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-gray-300 sm:w-auto sm:mt-0 sm:ml-3 sm:text-sm"
                        ref="cancelButtonRef">
            Cancelar
          </router-link>
        </footer>
      </form>
    </div>
  </template>
    
  <script setup>
  import {onMounted, ref} from 'vue'
  import CustomInput from "../../components/core/CustomInput.vue";
  import store from "../../store/index.js";
  import Spinner from "../../components/core/Spinner.vue";
  import {useRoute, useRouter} from "vue-router";
  import ImagePreview from "../../components/ImagePreview.vue";
  import {PlusCircleIcon, TrashIcon} from '@heroicons/vue/24/solid';
  // import the component
  import Treeselect from 'vue3-treeselect'
  // import the styles
  import 'vue3-treeselect/dist/vue3-treeselect.css'
  import axiosClient from "../../axios.js";
  
  const route = useRoute()
  const router = useRouter()
  
  const article = ref({
    id: null,
    title: null,
    subtitle: '',
    news_lead: '',
    description: '',
    images: [],
    deleted_images: [],
    image_positions: {},
    published: false,
    authors: [],
  })
  
  const errors = ref({});
  
  const loading = ref(false);
  const authorsOptions = ref([]);
  
  const emit = defineEmits(['update:modelValue', 'close'])
  
  onMounted(() => {
    if (route.params.id) {
      loading.value = true
      store.dispatch('getArticle', route.params.id)
        .then((response) => {
          loading.value = false;
          article.value = response.data;
        })
    }
    axiosClient.get('/authors/tree')
    .then(result => {
      authorsOptions.value = result.data;
    })
    .catch(error => {
      console.error("Error al obtener los autores:", error);
    });
  })
  
  function onSubmit($event, close = false) {
    loading.value = true
    errors.value = {};
    if (article.value.id) {
      store.dispatch('updateArticle', article.value)
        .then(response => {
          loading.value = false;
          if (response.status === 200) {
            article.value = response.data
            store.commit('showToast', 'Article was successfully updated');
            store.dispatch('getArticles')
            if (close) {
              router.push({name: 'app.articles'})
            }
          }
        })
        .catch(err => {
          loading.value = false;
          errors.value = err.response.data.errors
        })
    } else {
      store.dispatch('createArticle', article.value)
        .then(response => {
          loading.value = false;
          if (response.status === 201) {
            article.value = response.data
            store.commit('showToast', 'Article was successfully created');
            store.dispatch('getArticles')
            if (close) {
              router.push({name: 'app.articles'})
            } else {
              article.value = response.data
              router.push({name: 'app.articles.edit', params: {id: response.data.id}})
            }
          }
        })
        .catch(err => {
          loading.value = false;
          errors.value = err.response.data.errors
        })
    }
  }
  </script>
    