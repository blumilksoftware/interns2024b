<template>
  <form class="row-start-1 col-start-1 space-y-6" action="#" method="POST">
    <div>
      <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
      <div class="mt-2">
        <input id="email" name="email" type="email" autocomplete="email"
               class="bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-inset ring-primary/30 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
        >
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between">
        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
      </div>
      <div class="mt-2">
        <input id="password" name="password" type="password" autocomplete="current-password"
               class="bg-white/30 rounded-lg w-full p-3 text-gray-900 ring-2 ring-inset ring-primary/30 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6"
        >
      </div>
    </div>

    <div>
      <button type="submit" class="
              rounded-lg text-md flex w-full justify-center bg-primary p-3 font-bold text-white transition hover:bg-primary-950
              focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary
            "
      >
        Log In
      </button>
    </div>
  </form>
</template>


