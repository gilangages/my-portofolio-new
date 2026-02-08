export const getAllExperiences = async () => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/experiences`, {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
  });
};

export const getSingleExperience = async (id) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/experiences/${id}`, {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
  });
};

export const adminUploadExperience = async (
  token,
  { company_name, role, status, start_date, end_date, description },
) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/experiences}`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authoriazation: `Bearer ${token}`,
      Accept: "application/json",
    },
    body: JSON.stringify({ company_name, role, status, start_date, end_date, description }),
  });
};

export const adminUpdateExperience = async (
  token,
  id,
  { company_name, role, status, start_date, end_date, description },
) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/experiences/${id}`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
      Authoriazation: `Bearer ${token}`,
      Accept: "application/json",
    },
    body: JSON.stringify({ company_name, role, status, start_date, end_date, description }),
  });
};

export const adminDeleteExperience = async (token, id) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/experiences/${id}`, {
    method: "DELETE",
    headers: {
      Authoriazation: `Bearer ${token}`,
      Accept: "application/json",
    },
  });
};
