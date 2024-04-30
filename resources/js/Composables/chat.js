import { ref } from 'vue';

export default function useChat() {
    const messages = ref([]);
    const errors = ref([]);

    const getMessages = async () => {
        await axios.get('/chat')
            .then((response) => {
                messages.value = response.data;
        })
    }

    const addMessage = async (form) => {
        errors.value = [];

        try {
            await axios.post('/chat', form)
                .then((response) => {
                    messages.value.push(response.data);
            })
        } catch (e) {
            errors.value = e.response;
        }
    }

    return {
        messages,
        errors,
        getMessages,
        addMessage
    }
}