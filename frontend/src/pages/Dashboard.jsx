import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../contexts/AuthContext";
import api from "../services/api";
import SnippetCard from "../components/snippets/SnippetCard";
import "../styles/Dashboard.css";

const Dashboard = () => {
  const [snippets, setSnippets] = useState([]);
  const [filteredSnippets, setFilteredSnippets] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [languages, setLanguages] = useState([]);
  const [tags, setTags] = useState([]);
  const [showFavorites, setShowFavorites] = useState(false);

  // Filter states
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedLanguage, setSelectedLanguage] = useState("");
  const [selectedTag, setSelectedTag] = useState("");

  const { currentUser, logout } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [snippetsRes, languagesRes, tagsRes] = await Promise.all([
          api.get("/snippets"),
          api.get("/guest/languages"),
          api.get("/guest/tags"),
        ]);

        if (snippetsRes.data.success) {
          const snippetsData = snippetsRes.data.data.data || [];
          setSnippets(snippetsData);
          setFilteredSnippets(snippetsData);
        }

        if (languagesRes.data.success) {
          setLanguages(languagesRes.data.data);
        }

        if (tagsRes.data.success) {
          setTags(tagsRes.data.data);
        }
      } catch (err) {
        setError(
          "Error fetching data: " + (err.response?.data?.message || err.message)
        );
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  useEffect(() => {
    let result = snippets;

    if (searchTerm) {
      const term = searchTerm.toLowerCase();
      result = result.filter(
        (snippet) =>
          snippet.title.toLowerCase().includes(term) ||
          snippet.description?.toLowerCase().includes(term) ||
          snippet.code_content.toLowerCase().includes(term)
      );
    }

    if (selectedLanguage) {
      result = result.filter(
        (snippet) => snippet.language_id.toString() === selectedLanguage
      );
    }

    if (selectedTag) {
      result = result.filter((snippet) =>
        snippet.tags.some((tag) => tag.id.toString() === selectedTag)
      );
    }

    if (showFavorites) {
      const fetchFavorites = async () => {
        try {
          const response = await api.get("/favorites");
          if (response.data.success) {
            const favoritesData = response.data.data.data || [];
            setFilteredSnippets(favoritesData);
          }
        } catch (err) {
          setError(
            "Error fetching favorites: " +
              (err.response?.data?.message || err.message)
          );
        }
      };

      fetchFavorites();
      return;
    }

    setFilteredSnippets(result);
  }, [snippets, searchTerm, selectedLanguage, selectedTag, showFavorites]);

  const handleCreateNew = () => {
    navigate("/snippets/create");
  };

  const clearFilters = () => {
    setSearchTerm("");
    setSelectedLanguage("");
    setSelectedTag("");
    setShowFavorites(false);
  };

  return (
    <div className="dashboard">
      <header className="dashboard-header">
        <div className="dashboard-header-content">
          <h1>My Code Snippets</h1>
          <div className="dashboard-actions">
            <button className="create-button" onClick={handleCreateNew}>
              Create New Snippet
            </button>
            <button className="logout-button" onClick={logout}>
              Logout
            </button>
          </div>
        </div>
      </header>

      <main className="dashboard-content">
        <div className="welcome-section">
          <h2>Welcome, {currentUser?.name || "Developer"}!</h2>
          <p>Manage and organize your code snippets in one place</p>
        </div>

        <div className="filters-section">
          <div className="search-box">
            <input
              type="text"
              placeholder="Search snippets..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>

          <div className="filter-options">
            <select
              value={selectedLanguage}
              onChange={(e) => setSelectedLanguage(e.target.value)}
            >
              <option value="">All Languages</option>
              {languages.map((lang) => (
                <option key={lang.id} value={lang.id}>
                  {lang.name}
                </option>
              ))}
            </select>

            <select
              value={selectedTag}
              onChange={(e) => setSelectedTag(e.target.value)}
            >
              <option value="">All Tags</option>
              {tags.map((tag) => (
                <option key={tag.id} value={tag.id}>
                  {tag.name}
                </option>
              ))}
            </select>

            <button
              className={`favorites-toggle ${showFavorites ? "active" : ""}`}
              onClick={() => setShowFavorites(!showFavorites)}
            >
              {showFavorites ? "All Snippets" : "Favorites Only"}
            </button>

            <button className="clear-filters" onClick={clearFilters}>
              Clear Filters
            </button>
          </div>
        </div>

        {loading ? (
          <div className="loading">Loading snippets...</div>
        ) : error ? (
          <div className="error-message">{error}</div>
        ) : filteredSnippets.length === 0 ? (
          <div className="empty-state">
            <p>
              {showFavorites
                ? "You don't have any favorite snippets yet."
                : "No snippets match your filters."}
            </p>
            <button className="accent-button" onClick={clearFilters}>
              Clear Filters
            </button>
          </div>
        ) : (
          <div className="snippets-grid">
            {filteredSnippets.map((snippet) => (
              <SnippetCard key={snippet.id} snippet={snippet} />
            ))}
          </div>
        )}
      </main>
    </div>
  );
};

export default Dashboard;
