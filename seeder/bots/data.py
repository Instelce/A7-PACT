import json
from selenium import webdriver
from selenium.webdriver.chrome.service import Service as ChromeService
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.firefox.service import Service as FirefoxService
from webdriver_manager.firefox import GeckoDriverManager
from selenium.webdriver.common.by import By
from selenium.webdriver.support.wait import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
from datetime import datetime

# Offer
# title
# summary
# description
# website
# phone_number

# Activity offer
# duration
# required_age
# price

# Restaurant offer
# url_image_carte
# minimum_price
# maximum_price

# Load links
with open("../data/links.json", "r") as f:
    links = json.load(f)

# Set up the driver
options = Options()
options.add_argument("--headless")
driver = webdriver.Chrome(service=ChromeService(ChromeDriverManager().install()), options=options)
# driver = webdriver.Firefox(service=FirefoxService(GeckoDriverManager().install()))

def is_activity(url):
    return "/Attraction_" in url

def is_restaurant(url):
    return "/Restaurant_" in url

for link in links:
    driver.get(link)