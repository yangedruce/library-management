<template>
    <form @submit.prevent="handleSubmit" class="mb-6">
        <div class="flex items-center">
            <div>
                <input
                    v-model="form.title"
                    type="text"
                    placeholder="Title"
                    class="border p-2 mr-2"
                />
            </div>
            <input
                v-model="form.author"
                type="text"
                placeholder="Author"
                class="border p-2 mr-2"
            />
        </div>
        <textarea
            v-model="form.description"
            placeholder="Description"
            class="border p-2 block w-full mt-2"
        ></textarea>
        <div class="flex gap-2 my-2">
            <label
                v-for="category in categories"
                :key="category.id"
                class="flex items-center gap-1"
            >
                <input
                    type="checkbox"
                    :value="category.id"
                    v-model="form.categories"
                />
                {{ category.name }}
            </label>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2">
            {{ editBook ? "Update" : "Add" }}
        </button>
    </form>
</template>

<script setup>
import { ref, watch, reactive } from "vue";
import axios from "axios";

const props = defineProps(["categories", "editBook"]);
const emit = defineEmits(["submit", "added", "updated", "refresh", "error"]);

const form = reactive({
    title: "",
    author: "",
    description: "",
    categories: [],
});

watch(
    () => props.editBook,
    (newVal) => {
        if (newVal) {
            Object.assign(form, {
                title: newVal.title,
                author: newVal.author,
                description: newVal.description,
                categories: newVal.categories.map((c) => c.id),
            });
        } else {
            resetForm();
        }
    }
);

const handleSubmit = async () => {
    if (!form.title) {
        emit("error", "Title is required.");
        return;
    }

    try {
        if (props.editBook) {
            await axios.put(`/api/books/${props.editBook.id}`, form);
            emit("updated", true);
        } else {
            await axios.post("/api/books", form);
            emit("added");
        }
        resetForm();
        emit("refresh");
    } catch (error) {
        console.error("Error in handleSubmit:", error);
        emit("error", "An error occurred while submitting the form.");
    }
};

const resetForm = () => {
    Object.assign(form, {
        title: "",
        author: "",
        description: "",
        categories: [],
    });
};
</script>
