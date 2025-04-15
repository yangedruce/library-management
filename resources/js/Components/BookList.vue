<template>
    <div class="space-y-4">
        <div v-for="book in books" :key="book.id" class="border p-4 rounded">
            <div class="text-xl font-semibold">{{ book.title }}</div>
            <div class="text-gray-500">{{ book.author }}</div>
            <div>{{ book.description }}</div>
            <div class="text-sm text-blue-600">
                Categories: {{ book.categories.map((c) => c.name).join(", ") }}
            </div>
            <div class="mt-2 flex gap-2">
                <button
                    @click="$emit('edit', book)"
                    class="bg-yellow-500 text-white px-3 py-1"
                >
                    Edit
                </button>
                <button
                    @click="deleteBook(book.id)"
                    class="bg-red-500 text-white px-3 py-1"
                >
                    Delete
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
const props = defineProps(["books"]);
const emit = defineEmits(["edit", "deleted"]);

const deleteBook = async (id) => {
    await axios.delete(`/api/books/${id}`);
    emit("deleted");
};
</script>
