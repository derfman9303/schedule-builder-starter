import { ref } from 'vue';
import axios from 'axios';
import { type Department } from '@/types/Department';

const departments = ref<Department[]>([]);
const isLoading = ref(false);
const isLoaded = ref(false);

export function useDepartments() {
    const loadDepartments = async () => {
        if (isLoaded.value) {
            return;
        }

        isLoading.value = true;

        try {
            const response = await axios.get('/api/departments');
            departments.value = response.data;
            isLoaded.value = true;
        } catch (error) {
            console.error('Error loading departments:', error);
        } finally {
            isLoading.value = false;
        }
    };

    return {
        departments,
        isLoading,
        isLoaded,
        loadDepartments,
    };
}
