import { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import api from "../services/api";
import "../styles/SnippetForm.css";

const SnippetForm = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const isEditMode = !!id;

  const [formData, setFormData] = useState({
    title: "",
    code_content: "",
    language_id: "",
    description: "",
    tags: [],
  });

  const [languages, setLanguages] = useState([]);
  const [availableTags, setAvailableTags] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [selectedTags, setSelectedTags] = useState([]);
  const [newTagName, setNewTagName] = useState("");
  const [creatingTag, setCreatingTag] = useState(false);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [languagesRes, tagsRes] = await Promise.all([
          api.get("/guest/languages"),
          api.get("/guest/tags"),
        ]);

        if (languagesRes.data.success) {
          setLanguages(languagesRes.data.data);
        }

        if (tagsRes.data.success) {
          setAvailableTags(tagsRes.data.data);
        }

        if (isEditMode) {
          const snippetRes = await api.get(`/snippets/${id}`);
          if (snippetRes.data.success) {
            const snippet = snippetRes.data.data;
            setFormData({
              title: snippet.title,
              code_content: snippet.code_content,
              language_id: snippet.language_id,
              description: snippet.description || "",
            });

            if (snippet.tags) {
              setSelectedTags(snippet.tags.map((tag) => tag.id));
            }
          }
        }
      } catch (err) {
        setError(
          "Error loading data: " + (err.response?.data?.message || err.message)
        );
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [id, isEditMode]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleTagToggle = (tagId) => {
    setSelectedTags((prev) => {
      if (prev.includes(tagId)) {
        return prev.filter((id) => id !== tagId);
      } else {
        return [...prev, tagId];
      }
    });
  };

  const handleCreateTag = async (e) => {
    e.preventDefault();

    if (!newTagName.trim()) {
      return;
    }

    setCreatingTag(true);

    try {
      const tagExists = availableTags.some(
        (tag) => tag.name.toLowerCase() === newTagName.toLowerCase()
      );

      if (tagExists) {
        setError(`Tag "${newTagName}" already exists!`);
        setCreatingTag(false);
        return;
      }

      const response = await api.post("/guest/tags", { name: newTagName });

      if (response.data.success) {
        const newTag = response.data.data;

        setAvailableTags((prev) => [...prev, newTag]);

        setSelectedTags((prev) => [...prev, newTag.id]);

        setNewTagName("");

        setError(null);
      } else {
        setError("Failed to create tag");
      }
    } catch (err) {
      setError(
        "Error creating tag: " + (err.response?.data?.message || err.message)
      );
    } finally {
      setCreatingTag(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      const data = {
        ...formData,
        tags: selectedTags,
      };

      let response;

      if (isEditMode) {
        response = await api.put(`/snippets/${id}`, data);
      } else {
        response = await api.post("/snippets", data);
      }

      if (response.data.success) {
        navigate("/dashboard");
      } else {
        setError("Failed to save snippet");
      }
    } catch (err) {
      setError(
        "Error saving snippet: " + (err.response?.data?.message || err.message)
      );
    }
  };

  if (loading) {
    return <div className="loading">Loading...</div>;
  }

  return (
    <div className="snippet-form-container">
      <div className="form-header">
        <h2>{isEditMode ? "Edit Snippet" : "Create New Snippet"}</h2>
        <button
          className="cancel-button"
          onClick={() => navigate("/dashboard")}
        >
          Cancel
        </button>
      </div>

      {error && <div className="error-message">{error}</div>}

      <form onSubmit={handleSubmit} className="snippet-form">
        <div className="form-group">
          <label htmlFor="title">Title</label>
          <input
            type="text"
            id="title"
            name="title"
            value={formData.title}
            onChange={handleChange}
            placeholder="Name your snippet"
            required
          />
        </div>

        <div className="form-group">
          <label htmlFor="language_id">Language</label>
          <select
            id="language_id"
            name="language_id"
            value={formData.language_id}
            onChange={handleChange}
            required
          >
            <option value="">Select a language</option>
            {languages.map((lang) => (
              <option key={lang.id} value={lang.id}>
                {lang.name}
              </option>
            ))}
          </select>
        </div>

        <div className="form-group">
          <label htmlFor="code_content">Code</label>
          <textarea
            id="code_content"
            name="code_content"
            value={formData.code_content}
            onChange={handleChange}
            placeholder="Paste your code here"
            rows="10"
            required
          />
        </div>

        <div className="form-group">
          <label htmlFor="description">Description (optional)</label>
          <textarea
            id="description"
            name="description"
            value={formData.description}
            onChange={handleChange}
            placeholder="Add notes or description"
            rows="3"
          />
        </div>

        <div className="form-group">
          <label>Tags</label>
          <div className="new-tag-form">
            <input
              type="text"
              placeholder="Add a new tag..."
              value={newTagName}
              onChange={(e) => setNewTagName(e.target.value)}
            />
            <button
              type="button"
              onClick={handleCreateTag}
              disabled={creatingTag || !newTagName.trim()}
              className="create-tag-button"
            >
              {creatingTag ? "Creating..." : "Add Tag"}
            </button>
          </div>

          <div className="tags-container">
            {availableTags.map((tag) => (
              <div
                key={tag.id}
                className={`tag-item ${
                  selectedTags.includes(tag.id) ? "selected" : ""
                }`}
                onClick={() => handleTagToggle(tag.id)}
              >
                {tag.name}
              </div>
            ))}
          </div>
        </div>

        <div className="form-actions">
          <button type="submit" className="save-button">
            {isEditMode ? "Update Snippet" : "Save Snippet"}
          </button>
        </div>
      </form>
    </div>
  );
};

export default SnippetForm;
