export const getSkills = async () => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/skills`, {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
  });
};

export const addSkill = async (token, formData) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/skills`, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
    body: formData,
  });
};

export const deleteSkill = async (token, id) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/skills/${id}`, {
    method: "DELETE",
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
  });
};
