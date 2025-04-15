<template>
    <Head title="Library Management" />
    <div class="p-4 max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Library Management</h1>

        <div
            v-if="message"
            class="bg-green-100 text-green-800 p-2 rounded mb-4"
        >
            {{ message }}
        </div>

        <BookForm
            @added="handleBookAdded"
            @updated="handleBookUpdated"
            @refresh="fetchBooks"
            :categories="categories"
            :editBook="editBook"
        />
        <input
            v-model="search"
            type="text"
            placeholder="Search books..."
            class="border p-2 my-4 w-full"
        />

        <div class="flex gap-2 mb-4">
            <label
                v-for="category in categories"
                :key="category.id"
                class="flex items-center gap-1"
            >
                <input
                    type="checkbox"
                    :value="category.name"
                    v-model="selectedCategories"
                />
                {{ category.name }}
            </label>
        </div>

        <BookList
            :books="filteredBooks"
            @edit="handleBookEdit"
            @deleted="handleBookDeleted"
        />
    </div>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import BookForm from "../components/BookForm.vue";
import BookList from "../components/BookList.vue";

const books = ref([]);
const categories = ref([]);
const selectedCategories = ref([]);
const search = ref("");
const editBook = ref(null);
const message = ref("");

const fetchBooks = async (msg = null) => {
    books.value = await axios.get("/api/books").then((res) => res.data);
    if (msg) {
        showMessage(msg);
    }
};

const fetchCategories = async () => {
    categories.value = await axios
        .get("/api/categories")
        .then((res) => res.data);
};

const filteredBooks = computed(() => {
    return books.value.filter((book) => {
        const matchSearch = book.title
            .toLowerCase()
            .includes(search.value.toLowerCase());
        const matchCategory =
            selectedCategories.value.length === 0 ||
            book.categories.some((c) =>
                selectedCategories.value.includes(c.name)
            );
        return matchSearch && matchCategory;
    });
});

const showMessage = (msg) => {
    message.value = msg;
    setTimeout(() => {
        message.value = "";
    }, 3000);
};

const handleBookAdded = () => {
    fetchBooks("Book added successfully!");
};

const handleBookEdit = (book) => {
    editBook.value = book;
};

const handleBookUpdated = (updated) => {
    if (updated) {
        fetchBooks("Book updated successfully!");
        editBook.value = null;
    }
};

const handleBookDeleted = () => {
    fetchBooks("Book deleted successfully!");
};

onMounted(() => {
    fetchBooks();
    fetchCategories();
});
</script>
