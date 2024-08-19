<script setup lang="ts">
import AdminPanelLayout from '@/Layouts/AdminPanelLayout.vue'
import { ref } from 'vue'

const exampleData = ref([
  {id:1, entry1:'td', entry2:'rs'},
  {id:2, entry1:'te', entry2:'rd'},
  {id:3, entry1:'tr', entry2:'rf'},
  {id:4, entry1:'tt', entry2:'rg'},
  {id:5, entry1:'tu', entry2:'rh'},
])

function itemClicked(e:any){
  const element = (e.target as HTMLElement)
  const contentFrame : HTMLElement | null = document.getElementById('selectedItemContent')
  if (contentFrame)
    contentFrame.innerHTML = element.innerHTML
  // if (contentFrame && element.dataset.id)
  //   contentFrame.textContent = element.dataset.id
 
  
}

function addItem(){
  const data = exampleData.value
  data.push({id:data.length, entry1:'tu', entry2:'rh'})
}
</script>

<template>
  <AdminPanelLayout>
    <template #subheader>
      <button>filter</button>
      <button>sort</button>
      <div class="w-full" />
      <button @click="addItem">+&nbsp;add</button>
    </template>
    <template #items>
      <div v-for="item in exampleData" :key="item.id"
           class="cursor-pointer"
           :data-id="item.id"
           @click="itemClicked"
      >
        {{ item.entry1 }}&nbsp;{{ item.entry2 }}
      </div>
    </template>
    <template #selectedItemContent>
      <div id="selectedItemContent">content</div>
    </template>
  </AdminPanelLayout>
</template>
