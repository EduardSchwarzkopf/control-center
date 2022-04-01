<template>
  <div class="mt-2 mb-6 text-sm text-red-600" v-if="errors !== ''">
    {{ errors }}
  </div>

  <form class="space-y-6" v-on:submit.prevent="saveUser">
    <div class="space-y-4 rounded-md shadow-sm">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700"
          >Name</label
        >
        <div class="mt-1">
          <input
            type="text"
            name="name"
            id="name"
            class="
              block
              mt-1
              w-full
              rounded-md
              border-gray-300
              shadow-sm
              focus:border-indigo-300
              focus:ring
              focus:ring-indigo-200
              focus:ring-opacity-50
            "
            v-model="user.name"
          />
        </div>
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700"
          >E-Mail</label
        >
        <div class="mt-1">
          <input
            type="email"
            name="email"
            id="email"
            class="
              block
              mt-1
              w-full
              rounded-md
              border-gray-300
              shadow-sm
              focus:border-indigo-300
              focus:ring
              focus:ring-indigo-200
              focus:ring-opacity-50
            "
            v-model="user.email"
          />
        </div>
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700"
          >New Password</label
        >
        <div class="mt-1">
          <input
            type="password"
            name="password"
            id="password"
            class="
              block
              mt-1
              w-full
              rounded-md
              border-gray-300
              shadow-sm
              focus:border-indigo-300
              focus:ring
              focus:ring-indigo-200
              focus:ring-opacity-50
            "
            v-bind="{ user }"
            v-model="user.password"
          />
        </div>
      </div>
      <div>
        <label
          for="password_confirm"
          class="block text-sm font-medium text-gray-700"
          >Password confirm</label
        >
        <div class="mt-1">
          <input
            type="password"
            name="password_confirm"
            id="password-confirm"
            class="
              block
              mt-1
              w-full
              rounded-md
              border-gray-300
              shadow-sm
              focus:border-indigo-300
              focus:ring
              focus:ring-indigo-200
              focus:ring-opacity-50
            "
            v-model="user.password_confirm"
            v-bind="{ user }"
          />
        </div>
      </div>
      <div v-if="currentUser.is_admin">
        <label for="admin" class="block text-sm font-medium text-gray-700"
          >Admin</label
        >
        <div class="mt-1">
          <input
            type="checkbox"
            name="is_admin"
            id="is_admin"
            class="
              block
              mt-1
              rounded-md
              border-gray-300
              shadow-sm
              focus:border-indigo-300
              focus:ring
              focus:ring-indigo-200
              focus:ring-opacity-50
            "
            v-model="user.is_admin"
          />
        </div>
      </div>
    </div>

    <button
      type="submit"
      class="
        inline-flex
        items-center
        px-4
        py-2
        text-xs
        font-semibold
        tracking-widest
        text-white
        uppercase
        bg-gray-800
        rounded-md
        border border-transparent
        ring-gray-300
        transition
        duration-150
        ease-in-out
        hover:bg-gray-700
        active:bg-gray-900
        focus:outline-none focus:border-gray-900 focus:ring
        disabled:opacity-25
      "
    >
      Save
    </button>
  </form>
</template>

<script>
import useUsers from "../../composables/users";
import { onMounted } from "vue";

export default {
  props: {
    id: {
      required: true,
      type: String,
    },
  },

  setup(props) {
    const {
      errors,
      user,
      currentUser,
      getCurrentUser,
      updateUser,
      getUser,
      canEdit,
    } = useUsers();

    onMounted(async () => {
      await getCurrentUser();
      await canEdit(currentUser, props.id);

      getUser(props.id);
    });

    const saveUser = async () => {
      await updateUser(props.id);
    };

    return {
      errors,
      user,
      currentUser,
      saveUser,
    };
  },
};
</script>