<template>
  <q-layout view="hHh lpR fFf">
    <q-header elevated class="bg-primary text-white" height-hint="98">
      <q-toolbar>
        <q-toolbar-title class="row items-center no-wrap">
          <img
            src="https://consultareinc.com/wp-content/uploads/2024/05/consultare-logo.png"
            style="height: 40px; margin-right: 10px"
          />
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <q-page class="q-pa-md">
        <q-select
          v-model="statusFilter"
          :options="statusOptions"
          label="Filter by Status"
          dense
          options-dense
          class="q-mb-md"
        />
        <q-table
          title="Tasks"
          :rows="filteredRows"
          :columns="columns"
          row-key="id"
          :pagination="{ rowsPerPage: 10 }"
        >
          <template v-slot:top-right>
            <q-btn color="primary" label="Add Task" @click="openAddTaskModal" />
          </template>
          <template v-slot:body="props">
            <q-tr :props="props">
              <q-td key="id" :props="props">{{ props.row.id }}</q-td>
              <q-td key="title" :props="props">{{ props.row.title }}</q-td>
              <q-td key="description" :props="props">{{ props.row.description }}</q-td>
              <q-td key="status" :props="props">
                <q-chip
                  :color="getStatusColor(props.row.status)"
                  text-color="white"
                  dense
                >
                  {{ props.row.status }}
                </q-chip>
              </q-td>
              <q-td key="timestamp" :props="props">{{
                formatTimestamp(props.row.timestamp)
              }}</q-td>
              <q-td key="actions" :props="props">
                <q-btn
                  size="sm"
                  color="info"
                  label="Edit"
                  @click="openEditTaskModal(props.row)"
                  class="q-mr-sm"
                />
                <q-btn
                  size="sm"
                  color="negative"
                  label="Delete"
                  @click="deleteTask(props.row)"
                />
              </q-td>
            </q-tr>
          </template>
        </q-table>
      </q-page>
    </q-page-container>

    <!-- Add Task Modal -->
    <q-dialog v-model="addTaskModal" persistent>
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Add New Task</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-input v-model="newTask.title" label="Title" dense />
          <q-input
            v-model="newTask.description"
            label="Description"
            type="textarea"
            dense
          />
          <q-select
            v-model="newTask.status"
            :options="statusOptions"
            label="Status"
            dense
          />
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn flat label="Add Task" @click="addTask" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Edit Task Modal -->
    <q-dialog v-model="editTaskModal" persistent>
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Edit Task</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-input v-model="editedTask.title" label="Title" dense />
          <q-input
            v-model="editedTask.description"
            label="Description"
            type="textarea"
            dense
          />
          <q-select
            v-model="editedTask.status"
            :options="statusEditOptions"
            label="Status"
            dense
          />
        </q-card-section>

        <q-card-actions align="right" class="text-primary">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn flat label="Save Changes" @click="saveEditedTask" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
    <!-- Delete Confirmation Dialog -->
    <q-dialog v-model="deleteConfirmationDialog">
      <q-card>
        <q-card-section class="row items-center">
          <q-avatar icon="delete" color="negative" text-color="white" />
          <span class="q-ml-sm">Are you sure you want to delete this task?</span>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn
            flat
            label="Delete"
            color="negative"
            @click="confirmDelete"
            v-close-popup
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
    <!-- Notification -->
    <div class="notification-container">
      <div v-if="notification" :class="['notification', notification.type]">
        {{ notification.message }}
      </div>
    </div>
  </q-layout>
</template>

<script>
import { format } from "date-fns";
export default {
  data() {
    return {
      rows: [],
      columns: [
        { name: "id", align: "left", label: "ID", field: "id", sortable: true },
        { name: "title", align: "left", label: "Title", field: "title", sortable: true },
        {
          name: "description",
          align: "left",
          label: "Description",
          field: "description",
        },
        {
          name: "status",
          align: "left",
          label: "Status",
          field: "status",
          sortable: true,
        },
        {
          name: "timestamp",
          align: "left",
          label: "Timestamp",
          field: "timestamp",
          sortable: true,
        },
        { name: "actions", align: "center", label: "Actions", field: "actions" },
      ],
      statusEditOptions: ["Pending", "In Progress", "Completed"],
      addTaskModal: false,
      editTaskModal: false,
      newTask: {
        title: "",
        description: "",
        status: "Pending",
      },
      editedTask: {
        id: null,
        title: "",
        description: "",
        status: "",
      },
      deleteConfirmationDialog: false,
      taskToDelete: null,
      statusFilter: "All",
      statusOptions: ["All", "Pending", "In Progress", "Completed"],
      notification: null,
    };
  },
  computed: {
    /**
     * Return the rows filtered by the current statusFilter.
     *
     * If the statusFilter is "All", the original rows array is returned.
     * Otherwise, an array of rows filtered by the current statusFilter is returned.
     */
    filteredRows() {
      console.log("Rows: ", this.rows);

      if (this.statusFilter === "All") {
        return this.rows;
      }
      return this.rows.filter((row) => row.status === this.statusFilter);
    },
  },
  methods: {
    /**
     * Get the color code for a given status.
     *
     */
    getStatusColor(status) {
      switch (status) {
        case "Pending":
          return "orange";
        case "In Progress":
          return "blue";
        case "Completed":
          return "green";
        default:
          return "grey";
      }
    },
    /**
     * Format a timestamp into a human-readable date and time string.
     *The formatted date and time string in "MMM d, yyyy h:mm a" format.
     */
    formatTimestamp(timestamp) {
      return format(new Date(timestamp), "MMM d, yyyy h:mm a");
    },
    /**
     * Fetch tasks from the server and update the rows array.

     */
    async fetchTasks() {
      try {
        const response = await axios.get("/tasks");

        // Check if response.data exists and is an array
        if (response.data && Array.isArray(response.data)) {
          this.rows = response.data;
        } else {
          // If response.data is not an array or doesn't exist, set rows to an empty array
          this.rows = [];
        }

        // You might want to log the response to see its structure
        console.log("API Response:", response.data);
      } catch (error) {
        console.error("Error fetching tasks:", error);
        // Set rows to an empty array in case of error
        this.rows = [];
      }
    },
    /**
     * Open the add task modal.
     */
    openAddTaskModal() {
      this.addTaskModal = true;
    },
    /**
     * Open the edit task modal with the given task's data populated.
     *
     */
    openEditTaskModal(task) {
      Object.assign(this.editedTask, task);
      this.editTaskModal = true;
    },
    /**
     * Add a new task via the API and update the rows array.
     *
     */
    async addTask() {
      try {
        // Make the API call to add the task
        const response = await axios.post("/tasks", this.newTask);

        // Add the new task to the beginning of the rows array
        this.rows.unshift(response.data);

        // Reset the newTask object
        this.newTask = {
          title: "",
          description: "",
          status: "Pending",
        };

        // Close the add task modal
        this.addTaskModal = false;

        // Optionally, show a success message

        this.showNotification("Task added successfully", "success");
      } catch (error) {
        console.error("Error adding task:", error);
        // Show an error message

        this.showNotification("Failed to add task", "error");
      }
    },
    /**
     * Update the task with the given ID via the API and update the rows array.
     */
    async saveEditedTask() {
      console.log(this.editedTask);
      try {
        // Make the API call to update the task
        const response = await axios.put(`/tasks/${this.editedTask.id}`, this.editedTask);
        // Find the index of the task in the rows array
        const index = this.rows.findIndex((row) => row.id === this.editedTask.id);

        if (index !== -1) {
          // Update the task in the rows array with the response data
          this.rows[index] = response.data;
        }

        // Close the edit task modal
        this.editTaskModal = false;

        // Show a success message
        this.showNotification("Task updated successfully", "success");
      } catch (error) {
        console.error("Error updating task:", error);

        // Show an error message
        this.showNotification("Failed to update task", "error");
      }
    },

    /**
     * Show the delete confirmation dialog for the given task.
     *
     */
    deleteTask(row) {
      this.taskToDelete = row;
      this.deleteConfirmationDialog = true;
    },

    /**
     * Confirm and delete the selected task from the server and the local rows array.
     *
     */
    async confirmDelete() {
      if (!this.taskToDelete) return;

      try {
        await axios.delete(`/tasks/${this.taskToDelete.id}`);
        const index = this.rows.findIndex((r) => r.id === this.taskToDelete.id);
        if (index !== -1) {
          this.rows.splice(index, 1);
          this.showNotification("Task delete successfully", "success");
        }
      } catch (error) {
        console.error("Error deleting task:", error);
        this.showNotification("Task delete failed", "error");
      } finally {
        this.taskToDelete = null;
      }
    },
    showNotification(message, type) {
      this.notification = { message, type };
      setTimeout(() => {
        this.notification = null;
      }, 3000);
    },
  },
  /**
   * Fetches the list of tasks from the server when the component is mounted.
   */
  mounted() {
    this.fetchTasks();
  },
};
</script>
<style scoped>
.notification-container {
  position: fixed;
  top: 98px; /* Adjust this value to match your navbar height */
  right: 20px;
  z-index: 9999;
}
.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 10px;
  border-radius: 4px;
  color: white;
}
.success {
  background-color: green;
}
.error {
  background-color: red;
}
</style>
