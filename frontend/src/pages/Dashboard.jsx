import { useAuth } from '../contexts/AuthContext';

const Dashboard = () => {
  const { currentUser, logout } = useAuth();

  return (
    <div className="dashboard-container">
      <div className="dashboard-header">
        <h1>Dashboard</h1>
        <button onClick={logout} className="logout-button">Logout</button>
      </div>
      
      <div className="dashboard-content">
        <div className="welcome-card">
          <h2>Welcome, {currentUser?.name}!</h2>
          <p>You have successfully logged in to the Code Snippet Organizer.</p>
        </div>
        
        <div className="placeholder-content">
          <p>Your snippets will appear here once we build that functionality.</p>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;