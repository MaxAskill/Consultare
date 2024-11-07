<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { useQuasar } from "quasar";
import { ref } from "vue";

export default {
  setup() {
    const $q = useQuasar();

    const name = ref(null);
    const age = ref(null);
    const accept = ref(false);

    return {
      name,
      age,
      accept,

      onSubmit() {
        if (accept.value !== true) {
          $q.notify({
            color: "red-5",
            textColor: "white",
            icon: "warning",
            message: "You need to accept the license and terms first",
          });
        } else {
          $q.notify({
            color: "green-4",
            textColor: "white",
            icon: "cloud_done",
            message: "Submitted",
          });
        }
      },

      onReset() {
        name.value = null;
        age.value = null;
        accept.value = false;
      },
    };
  },
};
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-black dark:text-black">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="q-pa-md" style="max-width: 400px">
              <q-form @submit="onSubmit" @reset="onReset" class="q-gutter-md">
                <q-input
                  filled
                  v-model="name"
                  label="Your name *"
                  hint="Name and surname"
                  lazy-rules
                  :rules="[(val) => (val && val.length > 0) || 'Please type something']"
                />

                <q-input
                  filled
                  type="number"
                  v-model="age"
                  label="Your age *"
                  lazy-rules
                  :rules="[
                    (val) => (val !== null && val !== '') || 'Please type your age',
                    (val) => (val > 0 && val < 100) || 'Please type a real age',
                  ]"
                />

                <q-toggle v-model="accept" label="I accept the license and terms" />

                <div>
                  <q-btn label="Submit" type="submit" color="primary" />
                  <q-btn
                    label="Reset"
                    type="reset"
                    color="primary"
                    flat
                    class="q-ml-sm"
                  />
                </div>
              </q-form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
