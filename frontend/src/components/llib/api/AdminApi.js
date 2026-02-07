export const adminLogin = async ({ email, password }) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/admin/login`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
    body: JSON.stringify({
      email,
      password,
    }),
  });
};
