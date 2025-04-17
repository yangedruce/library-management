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

        <div
            v-if="errorMessage"
            class="bg-red-100 text-red-800 p-2 rounded mb-4">
            {{ errorMessage }}
        </div>

        <BookForm
            @added="handleBookAdded"
            @updated="handleBookUpdated"
            @refresh="fetchBooks"
            @error="showErrorMessage"
            :categories="categories"
            :editBook="editBook"
            @submit="handleFormSubmit"
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
const errorMessage = ref("");

const fetchBooks = async (msg = null) => {
    try {
        books.value = await axios.get("/api/books").then((res) => res.data);
        if (msg) {
            showMessage(msg);
        }
    } catch (error) {
        console.error("Error fetching books:", error);
        showErrorMessage("Failed to fetch books.");
    }
};

const fetchCategories = async () => {
    try {
        categories.value = await axios.get("/api/categories").then((res) => res.data);
    } catch (error) {
        console.error("Error fetching categories:", error);
        showErrorMessage("Failed to fetch categories.");
    }
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

const showErrorMessage = (error) => {
    errorMessage.value = error; 
    setTimeout(() => {
        errorMessage.value = ""; 
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
        showMessage("Book updated successfully!");
        fetchBooks();
        editBook.value = null;
    }
};

const handleBookDeleted = () => {
    fetchBooks("Book deleted successfully!");
};

const handleFormSubmit = async (form) => {
    if (!form.title) {
        showErrorMessage("Title is required.");
        return;
    }

    if (editBook.value) {
        await axios.put(`/api/books/${editBook.value.id}`, form);
        handleBookUpdated(form);
    } else {
        await axios.post("/api/books", form);
        handleBookAdded(form);
    }
};

onMounted(() => {
    fetchBooks();
    fetchCategories();
});
</script>
