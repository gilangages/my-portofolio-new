export const getProfile = async () => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/profile`, {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
  });
};

export const saveProfile = async (token, formData) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/profile`, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
    body: formData,
  });
};
