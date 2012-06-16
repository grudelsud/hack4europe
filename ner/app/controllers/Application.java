package controllers;

import java.io.File;
import java.io.IOException;
import java.util.*;

import org.codehaus.jackson.JsonNode;
import org.codehaus.jackson.map.ObjectMapper;
import org.codehaus.jackson.node.ArrayNode;
import org.codehaus.jackson.node.ObjectNode;
import org.jsoup.Jsoup;
import org.jsoup.nodes.*;
import org.jsoup.parser.Parser;
import org.jsoup.select.Elements;

import edu.stanford.nlp.ie.AbstractSequenceClassifier;
import edu.stanford.nlp.ie.crf.CRFClassifier;
import edu.stanford.nlp.ling.CoreLabel;

import play.*;
import play.data.*;
import play.libs.Json;
import play.mvc.*;

import views.html.*;

public class Application extends Controller {
	
	public static class FeedContent {
		public String content;
	}

	public static class EntityExtractor {
		private static String serializedClassifier = "/public/classifiers/english.all.3class.distsim.crf.ser.gz";
		
		public static HashMap<String, ArrayList<String>> Extract(String text) {
			ArrayList<String> people = new ArrayList<String>();
			ArrayList<String> organizations = new ArrayList<String>();
			ArrayList<String> locations = new ArrayList<String>();
			
			File classifierPath = play.Play.application().getFile(EntityExtractor.serializedClassifier);
			AbstractSequenceClassifier<CoreLabel> classifier = CRFClassifier.getClassifierNoExceptions(classifierPath.getAbsolutePath());
			String out = "<xml>" + classifier.classifyWithInlineXML(text).replaceAll("(\\[|\\])", "") + "</xml>";
			Document doc = Jsoup.parse(out, "", Parser.xmlParser());
			try {
				Elements el_people = doc.getElementsByTag("person");
				for(Element element : el_people) {
					people.add(element.html());
				}
				Elements el_organizations = doc.getElementsByTag("organization");
				for(Element element : el_organizations) {
					organizations.add(element.html());
				}
				Elements el_locations = doc.getElementsByTag("location");
				for(Element element : el_locations) {
					locations.add(element.html());
				}
				HashMap<String, ArrayList<String>> result = new HashMap<String, ArrayList<String>>();

				result.put("people", people);
				result.put("organizations", organizations);
				result.put("locations", locations);
				return result;
			} catch (Exception e) {
				e.printStackTrace();
			}
			return null;			
		}
	}

	public static Result index() {
		return ok(index.render(form(FeedContent.class)));
	}

	public static Result extractEntities() {
		Form<FeedContent> form = form(FeedContent.class).bindFromRequest();
		if(form.hasErrors()) {
			return badRequest(index.render(form));
		} else {
			FeedContent feedContent = form.get();
			HashMap<String, ArrayList<String>> result = EntityExtractor.Extract(feedContent.content);
			ObjectNode resultJson = Json.newObject();
			ObjectNode outputJson = Json.newObject();

			ObjectMapper mapper = new ObjectMapper();
			Map<String, JsonNode> outputMap = new HashMap<String, JsonNode>();
			Set<String> keys = result.keySet();
			for(String key: keys) {
				ArrayNode array = outputJson.putArray(key);
				for(String val: result.get(key)) {
					array.add(val);
				}
			}
			outputJson.putAll(outputMap);
			resultJson.put("success", outputJson);
			return ok(resultJson);
		}
	}
}