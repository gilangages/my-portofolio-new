export const getProfile = async () => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/profile`, {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
  });
};

export const uploadProfile = async (token, formData) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/profile`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
    body: formData,
  });
};

export const updateProfile = async (token, formData) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/profile`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
    body: formData,
  });
};
